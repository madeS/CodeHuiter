<?php

namespace CodeHuiter\Core\Exception;

use CodeHuiter\Core\Application;
use CodeHuiter\Core\Response;
use CodeHuiter\Service\ByDefault\PhpRenderer;
use CodeHuiter\Service\Logger;
use CodeHuiter\Core\Request;
use CodeHuiter\Exception\PhpErrorException;
use Exception;

/**
 * Class for Fatal framework errors
 */
class ExceptionProcessor
{
    /**
     * @param \Throwable $exception
     * @return void
     */
    public static function defaultProcessException(\Throwable $exception)
    {
        $dir = 'cli';
        $show_debug_backtrace = true;
        /** @var Exception[] $exceptions */
        $exceptions = [$exception];
        $show_errors = true;
        try {
            $app = Application::getInstance();
            /** @var Request $request */
            $request = $app->get(Request::class);

            $show_debug_backtrace = $app->config->frameworkConfig->showDebugBacktrace;
            $show_errors = $app->config->frameworkConfig->showErrors;

            if (!$request->isCli()) {
                $dir = 'html';
                $is_error = true;
                if ($exception instanceof PhpErrorException) {
                    $severity = $exception->getSeverity();
                    if (($severity & error_reporting()) !== $severity) {
                        $show_errors = false;
                    }
                    $is_error = (((E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR) & $severity) === $severity);
                }
                if ($is_error) {
                    header('HTTP/1.1' . ' ' . 500 . ' ' . Response::HTTP_CODES[500], TRUE, 500);
                }
            }
            /** @var Logger $log */
            $log = $app->get(Logger::class);
            if ($exception instanceof \CodeHuiter\Exception\PhpErrorException) {
                $log->withTag('exceptions')->error(
                    "{$exception->getMessage()} on {$exception->getErrorFile()}:{$exception->getErrorLine()}",
                    ['trace' => $exception->getTraceAsString()]
                );
            } else {
                $log->withTag('exceptions')->error(
                    "{$exception->getMessage()} on {$exception->getFile()}:{$exception->getLine()}",
                    ['trace' => $exception->getTraceAsString()]
                );
            }


        } catch (Exception $exceptionInner) {
            $exceptions[] = $exceptionInner;
        }
        if (!$show_errors) {
            return;
        }

        $exceptions = array_merge($exceptions, self::extractExceptions($exception));

        $templateFile = $dir . '/' . 'error_exception.php';
        $template = __DIR__ . '/' . 'Templates/' . $templateFile;

        if (file_exists(VIEW_PATH . 'default_error/' . $templateFile)) {
            $template = VIEW_PATH . 'default_error/' . $templateFile;
        }

        $obLevel = ob_get_level();
        /** @var PhpRenderer $phpRenderer */
        $phpRenderer = null;
        try {
            $phpRenderer =  Application::getInstance()->get(PhpRenderer::class);
        } catch (\Throwable $throwable) {
            $exceptions[] = $throwable;
        }

        if ($obLevel > ($phpRenderer ? $phpRenderer->getInitLevel() + 1 : 2)) {
            ob_end_flush();
        }

        ob_start();
        include $template;
        $buffer = ob_get_contents();
        @ob_end_clean();
        echo $buffer;
    }

    /**
    * @param int $severity
    * @param string $message
    * @param string $filepath
    * @param int $line
    * @return void
    */
    public static function defaultProcessError($severity, $message, $filepath, $line)
    {
        $phpException = new PhpErrorException($message);
        $phpException->setSeverity($severity);
        $phpException->setErrorFile($filepath);
        $phpException->setErrorLine($line);
        self::defaultProcessException($phpException);
    }

    /**
     * Result exception and previous exception as array
     * @param \Throwable $exception
     * @return \Throwable[]
     */
    public static function extractExceptions(\Throwable $exception) {
        $result = [];
        $result[] = $exception;
        $previous = $exception->getPrevious();
        if ($previous) {
            $previousResult = self::extractExceptions($previous);
            $result = array_merge($result, $previousResult);
        }
        return $result;
    }
}

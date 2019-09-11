<?php

namespace CodeHuiter\Core\Exception;

use CodeHuiter\Config\Config;
use CodeHuiter\Core\Application;
use CodeHuiter\Service\Logger;
use CodeHuiter\Core\Request;
use CodeHuiter\Core\Response;
use CodeHuiter\Exception\PhpErrorException;

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
        /** @var \Exception[] $exceptions */
        $exceptions = [];
        $show_errors = true;
        try {
            $app = Application::getInstance();
            /** @var Request $request */
            $request = $app->get(Config::SERVICE_KEY_REQUEST);

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
                    header('HTTP/1.1' . ' ' . 500 . ' ' . Response::$httpCodes[500], TRUE, 500);
                }
            }
            /** @var Logger $log */
            $log = $app->get(Config::SERVICE_KEY_LOG);
            $log->error($exception->getMessage(), ['trace' => $exception->getTraceAsString()], 'exceptions');

        } catch (\Exception $exceptionInner) {
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

        if (ob_get_level() > 1) {
            ob_end_flush();
        }

        ob_start();
        include($template);
        $buffer = ob_get_contents();
        ob_end_clean();
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

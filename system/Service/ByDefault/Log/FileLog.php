<?php
namespace CodeHuiter\Service\ByDefault\Log;

use CodeHuiter\Core\Exception\ExceptionProcessor;
use Exception;

class FileLog extends AbstractLog
{
    /**
     * @param string $message
     * @param mixed $context
     * @param string $level
     * @param string $tag
     */
    public function log(string $message, ?array $context = null, string $level = '', string $tag = ''): void
    {
        if ($level === '') {
            $level = $this->defaultLevel;
        }
        if (!in_array($this->levels[$level], $this->enableLevels, true)) {
            return;
        }

        $file = ($this->config->datePrepend ? date($this->config->datePrepend). '_' : '')
            . str_replace(
                ['{#tag}', '{#level}'],
                [$tag,     $level],
                $this->config->byFile
            ) . '.log';

        $message = $this->contextToString($message, $context);
        $this->writeToFile($this->config->directory . $file, $level, $message);
    }

    /**
     * @param string $message
     * @param mixed $context
     * @return string
     */
    protected function contextToString($message, $context)
    {
        $result = $message;
        if ($context === null) {

        } elseif ($context instanceof Exception) {
            $exceptions = ExceptionProcessor::extractExceptions($context);
            foreach($exceptions as $exception) {
                $result .= "\n\n"
                    . $exception->getMessage() . '['
                    . $exception->getFile() . ':' . $exception->getLine() . ']'
                    . "\n" . $exception->getTraceAsString();
            }
        } else {
            $result .= "\n\n" . print_r($context, true);
        }

        if ($this->traceData !== null) {
            $result .= "\nTrace:\n" . print_r($this->traceData, true);
            $this->traceData = null;
        }

        return $result;


    }

    /**
     * @param string $file
     * @param string $level
     * @param string $message
     */
    protected function writeToFile($file, $level, $message)
    {
        $isNewFile = false;
        if (!file_exists($file)) {
            $isNewFile = true;
        }

        $fp = fopen($file, 'ab');
        flock($fp, LOCK_EX);
        $timeString = '[' . $level . ' | ' . date($this->config->dateFormat) . ']';
        fwrite($fp, $timeString .' '. $message ."\n");
        flock($fp, LOCK_UN);
        fclose($fp);

        if ($isNewFile) {
            chmod($file, $this->config->filePermission);
        }
    }
}

<?php
namespace CodeHuiter\Exceptions;

use Throwable;

class PhpErrorException extends CodeHuiterException
{
    /** @var mixed $severity */
    protected $severity;

    /** @var string $errorFile */
    protected $errorFile;

    /** @var int $errorLine */
    protected $errorLine;

    /**
     * @param mixed $severity
     */
    public function setSeverity($severity)
    {
        $this->severity = $severity;
    }

    /**
     * @param string $errorFile
     */
    public function setErrorFile($errorFile)
    {
        $this->errorFile = $errorFile;
    }

    /**
     * @param int $errorLine
     */
    public function setErrorLine($errorLine)
    {
        $this->errorLine = $errorLine;
    }

    public function getSeverity()
    {
        return $this->severity;
    }

    public function getErrorFile()
    {
        return $this->errorFile;
    }

    public function getErrorLine()
    {
        return $this->errorLine;
    }
}

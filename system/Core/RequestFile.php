<?php

namespace CodeHuiter\Core;

class RequestFile
{
    /**
     * @var string
     */
    private $tmpFile;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $error;

    public function __construct(
        string $tmpFile,
        string $name,
        string $error
    ) {
        $this->tmpFile = $tmpFile;
        $this->name = $name;
        $this->error = $error;
    }

    public function getTmpFile(): string
    {
        return $this->tmpFile;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getError(): string
    {
        return $this->error;
    }
}

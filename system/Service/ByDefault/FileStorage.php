<?php

namespace CodeHuiter\Service\ByDefault;

use CodeHuiter\Service\Logger;
use RuntimeException;

class FileStorage implements \CodeHuiter\Service\FileStorage
{
    private const PERMISSIONS = 0777;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function isDirectoryExist($directory): bool
    {
        return file_exists($directory) && is_dir($directory);
    }

    public function setDefaultChmod(string $file): void
    {
        chmod($file, self::PERMISSIONS);
    }

    public function copyFile(string $fromFile, string $toDirectory, string $fileName): void
    {
        if (!file_exists($toDirectory)){
            if (!mkdir($toDirectory, self::PERMISSIONS, true) && !is_dir($toDirectory)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $toDirectory));
            }
            chmod($toDirectory, self::PERMISSIONS);
        }
        if (!is_dir($toDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" is not directory', $toDirectory));
        }

        copy($fromFile, $toDirectory . $fileName);
        chmod($toDirectory . $fileName, self::PERMISSIONS);
    }

    public function deleteFile(string $file): void
    {
        if(file_exists($file) && is_file($file)){
            unlink($file);
        } else {
            $this->logger->withTag('MEDIA')->warning("Remove file that already can not find [$file]");
        }
    }
}
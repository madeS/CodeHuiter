<?php

namespace CodeHuiter\Service;

interface FileStorage
{
    public function isDirectoryExist($directory): bool;

    public function setDefaultChmod(string $file): void;

    public function copyFile(string $fromFile, string $toDirectory, string $fileName): void;

    public function deleteFile(string $file): void;
}


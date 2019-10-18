<?php

namespace CodeHuiter\Service;

interface MimeTypeConverter
{
    public function getTypeHeader(string $extensionOrFilename, string $charset = null): string;

    public function getType(string $extensionOrFilename): string;
}

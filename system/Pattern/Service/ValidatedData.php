<?php

namespace CodeHuiter\Pattern\Service;

interface ValidatedData
{
    public function hasField(string $key): bool;

    public function setField(string $key, string $value, bool $fieldExist): void;

    public function getField(string $key): string;
}

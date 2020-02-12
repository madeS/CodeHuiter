<?php

namespace CodeHuiter\Facilities\Service\ByDefault;

class ValidatedData implements \CodeHuiter\Facilities\Service\ValidatedData
{
    private $fields = [];

    private $existField = [];

    public function hasField(string $key): bool
    {
        return isset($this->existField[$key]);
    }

    public function setField(string $key, string $value, bool $fieldExist): void
    {
        if ($fieldExist) {
            $this->existField[$key] = true;
        }
        $this->fields[$key] = $value;
    }

    public function getField(string $key): string
    {
        return $this->fields[$key] ?? '';
    }
}

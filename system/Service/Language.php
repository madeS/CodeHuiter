<?php

namespace CodeHuiter\Service;

interface Language
{
    /**
     * @param string $language
     */
    public function setLanguage($language): void;

    /**
     * Get string in language volume
     * @param string $alias alias of the string
     * @param array $replacePairs key -> value replace pairs
     * @return string
     */
    public function get($alias, $replacePairs = []): string;
}

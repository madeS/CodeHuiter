<?php

namespace CodeHuiter\Modifiers;


class StringModifier
{
    /**
     * @param string $textPattern Init string
     * @param array $replacePairs key value replaces
     * @return string
     */
    public static function replace($textPattern, $replacePairs = [])
    {
        $result = $textPattern;
        foreach ($replacePairs as $key => $value) {
            $result = str_replace($key, $value, $result);
        }
        return $result;
    }

    const FILTER_BASIC_EN = 1;
    const FILTER_BASIC_EN_RU = 2;

    public static function filterChars($textPattern, $filterOption)
    {
        $result = $textPattern;
        if (is_array($filterOption)) {
            foreach ($filterOption as $option) {
                $result = self::filterChars($result, $option);
            }
        }
        if (is_int($filterOption)) {
            if ($filterOption === self::FILTER_BASIC_EN) {
                $result = preg_replace('%[^A-Za-z0-9_-]%', '', $result);
            }
            if ($filterOption === self::FILTER_BASIC_EN_RU) {
                $result = preg_replace('%[^A-Za-zА-Яа-я0-9_-]%', '', $result);
            }
        }

        return $result;
    }

    /**
     * Encode to JSON
     * @param array $param
     * @return string
     */
    public static function jsonEncode($param)
    {
        return json_encode($param, JSON_UNESCAPED_SLASHES ^ JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param string $encoded json string
     * @param bool $force return empty array if json is broken
     * @return array|bool
     */
    public static function jsonDecode($encoded, $force = true)
    {
        if ($encoded === '') {
            return ($force) ? [] : false;
        }
        $result = json_decode($encoded, true);
        if (json_last_error()) {
            return ($force) ? [] : false;
        }
        return $result;
    }

}

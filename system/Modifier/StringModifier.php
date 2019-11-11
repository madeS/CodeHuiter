<?php

namespace CodeHuiter\Modifier;


class StringModifier
{
    private const CHARSET = 'UTF-8';
    public const FILTER_BASIC_EN = 1;
    public const FILTER_BASIC_EN_RU = 2;
    public const FILTER_BASIC_EN_BY = 3;

    public static function mbInit()
    {
        mb_internal_encoding(self::CHARSET);
        mb_regex_encoding(self::CHARSET);
    }

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

    public static function toLower(string $string)
    {
        return mb_strtolower($string, self::CHARSET);
    }

    public static function toUpper(string $string)
    {
        return mb_strtoupper($string, self::CHARSET);
    }

    public static function sub(string $string, $start, $length = null)
    {
        return mb_substr($string, $start, $length, self::CHARSET);
    }

    public static function pos(string $haystack, $needle, $offset = 0)
    {
        return mb_strpos($haystack, $needle, $offset, self::CHARSET);
    }

    /**
     * Filter risk content from HTML string to insert to HTML
     * @param string $string
     * @return string
     */
    public static function htmlForHtml(string $string): string
    {
        // use HTMLPurifier
        return $string;
    }

    /**
     * Filter risk content from non HTML string to insert to HTML
     * @param string $string
     * @return string
     */
    public static function textForHtml(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, self::CHARSET);
    }

    public static function fromHtml(string $string): string
    {
        // TODO high
    }

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
                $result = preg_replace('/[^A-Za-z0-9_-]/ui', '', $result);
            }
            if ($filterOption === self::FILTER_BASIC_EN_RU) {
                $result = preg_replace('/[^A-Za-zА-Яа-я0-9_-]/ui', '', $result);
            }
            if ($filterOption === self::FILTER_BASIC_EN_BY) {
                $result = preg_replace('/[^A-Za-zА-Яа-яіў0-9_-]/ui', '', $result);
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
     * @return array|null
     */
    public static function jsonDecode(string $encoded, bool $force = true): ?array
    {
        if ($encoded === '') {
            return ($force) ? [] : null;
        }
        $result = json_decode($encoded, true);
        if (json_last_error()) {
            return ($force) ? [] : null;
        }
        return $result;
    }

}

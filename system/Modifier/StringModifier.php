<?php

namespace CodeHuiter\Modifier;


use CodeHuiter\Exception\InvalidFlowException;

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
     * get word for 1 2 5th count (russian)
     * Вставляет слово со склонением в зависимости от числа
     * @param int $count
     * @param string $word1
     * @param string $word2
     * @param string $word5
     * @return string
     */
    public static function wordEnd(int $count, string $word1, string $word2, string $word5): string
    {
        $num = $count % 100;
        if ($count > 19) { $num=$num % 10; }
        switch ($num) {
            case 1:  { return $word1; }
            case 2: case 3: case 4:  { return $word2; }
            default: { return $word5; }
        }
    }

    public static function fillWordEnd(int $count, string $word125String, bool $fillCount = true): string
    {
        $word125Arr = explode(';', $word125String);
        if (!isset($word125Arr[1])) $word125Arr[1] = $word125Arr[0];
        if (!isset($word125Arr[2])) $word125Arr[2] = $word125Arr[0];
        return (($fillCount ? $count . ' '  : ' ')
            . self::wordEnd($count, $word125Arr[0], $word125Arr[1], $word125Arr[2]));
    }

    /**
     * Convert between two date formats
     * <br/> m : 2014-05-22
     * <br/> en : 05/22/2014
     * <br/> ru : 22.05.2014
     * @param string $input Input date string
     * @param string $type  m-en, m-ru, ru-m, en-m, ru-en, en-ru
     * @return string
     */
    public static function dateConvert(string $input, string $type = 'm-en'): string
    {
        $type_arr = explode('-', $type);
        if (count($type_arr) !== 2) {
            throw InvalidFlowException::onInvalidArgument('dateConvertType', $type);
        }
        [$from, $to] = $type_arr;
        $m_date = $input;
        if ($from === 'en') {
            $date_arr = explode('/', $input);
            if (count($date_arr) !== 3) {
                throw InvalidFlowException::onInvalidArgument('dateConvertEnInput', $input);
            }
            $m_date = str_pad((int)$date_arr[2], 4, '0', STR_PAD_LEFT).'-'
                .str_pad((int)$date_arr[0], 2, '0', STR_PAD_LEFT).'-'
                .str_pad((int)$date_arr[1], 2, '0', STR_PAD_LEFT);
        }
        if ($from === 'ru') {
            $date_arr = explode('.', $input);
            if (count($date_arr) != 3) {
                throw InvalidFlowException::onInvalidArgument('dateConvertRuInput', $input);
            }
            $m_date = str_pad((int)$date_arr[2], 4, '0', STR_PAD_LEFT).'-'
                .str_pad((int)$date_arr[1], 2, '0', STR_PAD_LEFT).'-'
                .str_pad((int)$date_arr[0], 2, '0', STR_PAD_LEFT);
        }
        $ret_date = $m_date;
        $date_arr = explode('-', $m_date);
        if ($to === 'm') {
            $ret_date = (int)$date_arr[0] . '-'
                . str_pad((int)$date_arr[1], 2, '0', STR_PAD_LEFT) . '-'
                . str_pad((int)$date_arr[2], 2, '0', STR_PAD_LEFT);
        }
        if ($to === 'en') {
            $ret_date = str_pad((int)$date_arr[1], 2, '0', STR_PAD_LEFT).'/'
                .str_pad((int)$date_arr[2], 2, '0', STR_PAD_LEFT).'/'
                . (int)$date_arr[0];
        }
        if ($to === 'ru') {
            $ret_date = str_pad((int)$date_arr[2], 2, '0', STR_PAD_LEFT).'.'
                .str_pad((int)$date_arr[1], 2, '0', STR_PAD_LEFT).'.'
                . (int)$date_arr[0];
        }
        return $ret_date;
    }

    public static function mbStrPad(string $input, int $pad_length, string $pad_string, $pad_style, string $encoding="UTF-8"): string
    {
        return str_pad($input,strlen($input)-mb_strlen($input,$encoding)+$pad_length, $pad_string, $pad_style);
    }

    /**
     * Encode to JSON
     * @param array $param
     * @param bool $pretty
     * @return string
     */
    public static function jsonEncode($param, bool $pretty = false): string
    {
        $options = JSON_UNESCAPED_SLASHES ^ JSON_UNESCAPED_UNICODE;
        if ($pretty) {
            $options ^= JSON_PRETTY_PRINT;
        }
        return json_encode($param, $options);
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

<?php

namespace CodeHuiter\Service\ByDefault;

class Debug implements \CodeHuiter\Service\Debug
{
    /**
     * @param mixed $obj
     * @param bool $toString
     * @return string
     */
    public function out($obj, bool $toString = false): string
    {
        $result = '';
        /** @noinspection ForgottenDebugOutputInspection */
        $result .= print_r($obj, true);
        if ($toString) {
            return $result;
        }
        echo $result;
        return '';
    }

    /**
     * @param mixed $obj
     * @return string
     */
    public function outDetailed($obj): string
    {
        /** @noinspection ForgottenDebugOutputInspection */
        var_dump($obj);
        return '';
    }

    /**
     * @param mixed $obj
     * @param bool $toString
     * @return string
     */
    public function outToHtml($obj, bool $toString = false): string
    {
        $result = '';
        $result .= '<pre>';
        /** @noinspection ForgottenDebugOutputInspection */
        $result .= print_r($obj, true);
        $result .= '</pre>';
        if ($toString) {
            return $result;
        }
        echo $result;
        return '';
    }

    /**
     * @param mixed $obj
     * @return string
     */
    public function outDetailedToHtml($obj): string
    {
        echo '<pre>';
        /** @noinspection ForgottenDebugOutputInspection */
        var_dump($obj);
        echo '</pre>';
        return '';
    }
}

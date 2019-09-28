<?php

namespace CodeHuiter\Service;

interface Debug
{
    /**
     * @param mixed $obj
     * @param bool $toString
     * @return string
     */
    public function out($obj, bool $toString = false): string;

    /**
     * @param mixed $obj
     * @return string
     */
    public function outDetailed($obj): string;

    /**
     * @param mixed $obj
     * @param bool $toString
     * @return string
     */
    public function outToHtml($obj, bool $toString = false): string;

    /**
     * @param mixed $obj
     * @return string
     */
    public function outDetailedToHtml($obj): string;
}

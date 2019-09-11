<?php

namespace CodeHuiter\Service;

class Debug
{
    /**
     * @param mixed $obj
     * @param array $options
     */
    public function out($obj, $options = []): void
    {
        $isHtml = $options['html'] ?? true;
        $detail = $options['detail'] ?? ($options['d'] ?? false);

        if ($isHtml) {
            echo '<pre>';
        }
        if ($detail) {
            /** @noinspection ForgottenDebugOutputInspection */
            var_dump($obj);
        } else {
            /** @noinspection ForgottenDebugOutputInspection */
            print_r($obj);
        }
        if ($isHtml) {
            echo '</pre>';
        }
    }
}

<?php

namespace CodeHuiter\Modifier;

class Debugger
{
    public static function log($message): void
    {
        if(is_object($message) || is_array($message)){
            $message = print_r($message,true);
        } else {
            $message = (string) $message;
        }
        print($message);
    }
}

<?php

namespace CodeHuiter\Modifiers;

class Validator
{
    /**
     * Check valid Email syntax
     * @param string $email
     * @return bool
     */
    public static function isValidEmail($email)
    {
        return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email))
            ? false
            : true;
        //return (bool) filter_var($str, FILTER_VALIDATE_EMAIL);
    }


}
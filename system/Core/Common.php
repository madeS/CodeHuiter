<?php

if ( ! function_exists('is_php'))
{
    /**
     * Determines if the current version of PHP is equal to or greater than the supplied value
     *
     * @param	string
     * @return	bool	TRUE if the current version is $version or higher
     */
    function is_php($version)
    {
        static $_is_php;
        $version = (string) $version;

        if ( ! isset($_is_php[$version]))
        {
            $_is_php[$version] = version_compare(PHP_VERSION, $version, '>=');
        }

        return $_is_php[$version];
    }
}

if ( ! function_exists('_error_handler'))
{
    /**
     * @param	int	$severity
     * @param	string	$message
     * @param	string	$filePath
     * @param	int	$line
     * @return	void
     */
    function _error_handler($severity, $message, $filePath, $line)
    {
        \CodeHuiter\Core\Exception\ExceptionProcessor::defaultProcessError($severity, $message, $filePath, $line);
    }
}

if ( ! function_exists('_exception_handler'))
{
    /**
     * @param $exception
     */
    function _exception_handler($exception)
    {
        \CodeHuiter\Core\Exception\ExceptionProcessor::defaultProcessException($exception);
    }
}

if ( ! function_exists('_shutdown_handler'))
{
    function _shutdown_handler()
    {
        $last_error = error_get_last();
        if (isset($last_error) &&
            ($last_error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING)))
        {
            _error_handler($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
        }
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('function_usable'))
{
    /**
     * Function usable
     *
     * Executes a function_exists() check, and if the Suhosin PHP
     * extension is loaded - checks whether the function that is
     * checked might be disabled in there as well.
     *
     * This is useful as function_exists() will return FALSE for
     * functions disabled via the *disable_functions* php.ini
     * setting, but not for *suhosin.executor.func.blacklist* and
     * *suhosin.executor.disable_eval*. These settings will just
     * terminate script execution if a disabled function is executed.
     *
     * The above described behavior turned out to be a bug in Suhosin,
     * but even though a fix was committed for 0.9.34 on 2012-02-12,
     * that version is yet to be released. This function will therefore
     * be just temporary, but would probably be kept for a few years.
     *
     * @link	http://www.hardened-php.net/suhosin/
     * @param	string	$function_name	Function to check for
     * @return	bool	TRUE if the function exists and is safe to call,
     *			FALSE otherwise.
     */
    function function_usable($function_name)
    {
        static $_suhosin_func_blacklist;

        if (function_exists($function_name))
        {
            if ( ! isset($_suhosin_func_blacklist))
            {
                $_suhosin_func_blacklist = extension_loaded('suhosin')
                    ? explode(',', trim(ini_get('suhosin.executor.func.blacklist')))
                    : array();
            }

            return ! in_array($function_name, $_suhosin_func_blacklist, TRUE);
        }

        return FALSE;
    }
}

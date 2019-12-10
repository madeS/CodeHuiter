<?php

if (!function_exists('_error_handler')) {
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

if (!function_exists('_exception_handler')) {
    /**
     * @param $exception
     */
    function _exception_handler($exception)
    {
        \CodeHuiter\Core\Exception\ExceptionProcessor::defaultProcessException($exception);
    }
}

if ( ! function_exists('_shutdown_handler')) {
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

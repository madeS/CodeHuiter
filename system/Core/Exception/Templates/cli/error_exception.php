<?php
/** @var \Exception[] $exceptions */
/** @var bool $show_debug_backtrace */

$output = '[!APP-FAILED!] ';

foreach ($exceptions as $exception) {
    $message = $exception->getMessage();
    $fileName = $exception->getFile();
    $fileLine = $exception->getLine();

    if ($exception instanceof \CodeHuiter\Exception\PhpErrorException) {
        $output .= "A PHP Error [{$exception->getSeverity()}] was encountered" . "\n";
        $fileName = $exception->getErrorFile();
        $fileLine = $exception->getErrorLine();
    } else {
        $output .= "An uncaught Exception was encountered" . "\n";
    }
    $output .= "Type:        " . get_class($exception) . "\n";

    $output .= "Type:        " . get_class($exception) . "\n";
    $output .= "Message:     " . $message . "\n";
    $output .= "Filename:    " . $fileName . "\n";
    $output .= "Line Number: " . $fileLine . "\n";

    if ($show_debug_backtrace) {
        $output .= "Backtrace:" . "\n";
        foreach ($exception->getTrace() as $error) {
            if (isset($error['file'])) {
                $output .= "File:     " . str_replace(BASE_PATH,'BASE_PATH/',$error['file']) . "\n";
                $output .= "Line:     " . $error['line'] . "\n";
                $output .= "Function: " . $error['function'] . "\n";
            }
        }
    }
}

echo $output;

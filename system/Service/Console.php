<?php

namespace CodeHuiter\Service;

interface Console
{
    /**
     * Log message to console
     * @param mixed $message Message
     * @param bool $clearLine Clear line (only if message without endLines)
     * @param bool $endLine Is end of line (not possible to clear in feature)
     */
    public function log($message, $clearLine = false, $endLine = true): void;

    /**
     * Return time as string, that show ETA
     *
     * @param int $now
     * @param int $total
     * @return string
     */
    public function progressRemaining(int $now, int $total): string;
}

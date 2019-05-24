<?php

namespace CodeHuiter\Core\Exception;

interface ExceptionThrowerInterface
{
    /**
     * @param \Exception $exception
     */
    public function fire(\Exception $exception): void;
}

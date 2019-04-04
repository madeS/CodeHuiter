<?php

namespace CodeHuiter\Core\Exceptions;

interface ExceptionThrowerInterface
{
    /**
     * @param \Exception $exception
     */
    public function fire(\Exception $exception): void;
}

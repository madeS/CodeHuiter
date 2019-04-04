<?php

namespace CodeHuiter\Core\Exceptions;

class ExceptionThrower implements ExceptionThrowerInterface
{
    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function fire(\Exception $exception): void
    {
        throw $exception;
    }
}

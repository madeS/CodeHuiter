<?php

namespace CodeHuiter\Core\Exception;

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

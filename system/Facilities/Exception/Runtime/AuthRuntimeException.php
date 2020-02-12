<?php
namespace CodeHuiter\Facilities\Exception\Runtime;

use CodeHuiter\Exception\CodeHuiterRuntimeException;

class AuthRuntimeException extends CodeHuiterRuntimeException
{
    public static function passFunctionMethodNotImplemented(string $methodName): AuthRuntimeException
    {
        return new self("Hash password function for method [{$methodName}] not implemented");
    }

}

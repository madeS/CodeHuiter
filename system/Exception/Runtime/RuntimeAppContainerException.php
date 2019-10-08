<?php
namespace CodeHuiter\Exception\Runtime;

use CodeHuiter\Exception\CodeHuiterException;

class RuntimeAppContainerException extends CodeHuiterException
{
    public static function appContainerReturnWrongType(string $expectedClass, string $returnedClass): RuntimeAppContainerException
    {
        return new RuntimeAppContainerException(
            "Application container return wrong object. Expected: $expectedClass, Returned: $returnedClass"
        );
    }
}

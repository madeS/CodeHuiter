<?php
namespace CodeHuiter\Exception\Runtime;

use CodeHuiter\Exception\CodeHuiterRuntimeException;

class RuntimeWrongClassException extends CodeHuiterRuntimeException
{
    public static function wrongObjectGot(string $expectedClass, $obj): RuntimeWrongClassException
    {
        $returnedClass = get_class($obj);
        return new RuntimeWrongClassException(
            "Runtime application wrong object. Expected: $expectedClass, Got: $returnedClass"
        );
    }
}

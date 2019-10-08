<?php
namespace CodeHuiter\Exception\Runtime;

use CodeHuiter\Exception\CodeHuiterException;

class RuntimeWrongClassException extends CodeHuiterException
{
    public static function wrongObjectGot(string $expectedClass, $obj): RuntimeAppContainerException
    {
        $returnedClass = get_class($obj);
        return new RuntimeAppContainerException(
            "Runtime application wrong object. Expected: $expectedClass, Got: $returnedClass"
        );
    }
}

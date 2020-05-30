<?php
namespace CodeHuiter\Exception\Runtime;

use CodeHuiter\Exception\CodeHuiterRuntimeException;
use Exception;

class CoreException extends CodeHuiterRuntimeException
{
    public static function onErrorControllerNoFound(
        string $controllerName,
        string $controllerMethod,
        Exception $exception
    ): CoreException {
        return new self(
            "Can't found '{$controllerName}::{$controllerMethod}' for call error 404",
            0,
            $exception
        );
    }

    public static function onInvalidRequireVarType(
        string $fileName,
        string $returnedType,
        string $expectedType
    ): CoreException {
        return new self(
            "Invalid type returned from [{$fileName}] Returned: [{$returnedType}], Expected: [{$expectedType}]"
        );
    }

    public static function onRecursiveServiceCreation(string $serviceName, string $scope, array $serviceStack): CoreException
    {
        return new self(
            "Recursive Service [$serviceName] creation found in scope [$scope]: " . print_r($serviceStack, true)
        );
    }

    public static function onServiceNotFound(string $serviceName): CoreException
    {
        return new self("Service [$serviceName] is not registered");
    }

    public static function onServiceConfigInvalid(string $serviceName): CoreException
    {
        return new self("Service Config for [$serviceName] invalid");
    }

    public static function onServiceNotProvideCreationInfo(string $serviceName): CoreException
    {
        return new self("Class [$serviceName] not provide object creation information");
    }

    public static function onServiceValidationNotPassed(
        string $serviceName,
        string $resultType,
        string $expectedType
    ): CoreException {
        return new self(
            "Class [$serviceName] provide object with validation fail. Expect: {$expectedType}, got {$resultType}"
        );
    }


}

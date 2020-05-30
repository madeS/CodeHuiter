<?php

namespace CodeHuiter\Config\Core;

class ServiceConfig
{
    public const TYPE_CLASS_APP = 'class_app';
    public const TYPE_CLASS = 'class';
    public const TYPE_CALLBACK = 'callback';

    public const SCOPE_PERMANENT = 'scope_permanent';
    public const SCOPE_REQUEST = 'scope_request';
    public const SCOPE_NO_SHARED = 'scope_no_shared';
    /**
     * @see ServiceConfig::TYPE_CLASS
     * @see ServiceConfig::TYPE_CLASS_APP
     * @see ServiceConfig::TYPE_CALLBACK
     * @var string
     */
    public $type;

    /**
     * @var string|null
     */
    public $className;

    /**
     * @var callable|null
     */
    public $callback;

    /**
     * @see ServiceConfig::SCOPE_PERMANENT as default id null
     * @see ServiceConfig::SCOPE_REQUEST
     * @see ServiceConfig::SCOPE_NO_SHARED
     * @var string|null
     */
    public $scope;

    /**
     * Validate className or null for validate by serviceName
     * @var string|null
     */
    public $validateClassName;

    public static function forClass(string $className, ?string $scope = null, ?string $validateClassName = null): self
    {
        $config = new self();
        $config->type = self::TYPE_CLASS;
        $config->className = $className;
        $config->scope = $scope;
        $config->validateClassName = $validateClassName;
        return $config;
    }

    public static function forAppClass(string $className, ?string $scope = null, ?string $validateClassName = null): self
    {
        $config = new self();
        $config->type = self::TYPE_CLASS_APP;
        $config->className = $className;
        $config->scope = $scope;
        $config->validateClassName = $validateClassName;
        return $config;
    }

    public static function forCallback(callable $callback, ?string $scope = null, ?string $validateClassName = null): self
    {
        $config = new self();
        $config->type = self::TYPE_CALLBACK;
        $config->callback = $callback;
        $config->scope = $scope;
        $config->validateClassName = $validateClassName;
        return $config;
    }
}
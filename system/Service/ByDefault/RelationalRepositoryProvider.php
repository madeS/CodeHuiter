<?php

namespace CodeHuiter\Service\ByDefault;

use CodeHuiter\Config\Database\RelationalRepositoryConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Database\RelationalRepository;
use CodeHuiter\Exception\InvalidConfigException;

class RelationalRepositoryProvider implements \CodeHuiter\Service\RelationalRepositoryProvider
{
    /**
     * @var Application
     */
    private $application;
    /**
     * @var RelationalRepository[]
     */
    private $cache;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function get(string $modelName): RelationalRepository
    {
        if (!isset($this->cache[$modelName])) {
            if (!isset($this->application->config->databaseConfig->models[$modelName])) {
                throw new InvalidConfigException(sprintf('repositoryConfigs has no model with name %s', $modelName));
            }
            $modelRepositoryConfig = $this->application->config->databaseConfig->models[$modelName];
            if (!$modelRepositoryConfig instanceof RelationalRepositoryConfig) {
                throw new InvalidConfigException(sprintf('repositoryConfig with name %s not instance of relational config ', $modelName));
            }
            $this->cache[$modelName] = new RelationalRepository($this->application, $modelRepositoryConfig);
        }
        return $this->cache[$modelName];
    }
}
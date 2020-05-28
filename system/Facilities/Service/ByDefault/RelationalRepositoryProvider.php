<?php

namespace CodeHuiter\Facilities\Service\ByDefault;

use CodeHuiter\Core\Application;
use CodeHuiter\Database\RelationalRepository;
use CodeHuiter\Exception\InvalidConfigException;

class RelationalRepositoryProvider implements \CodeHuiter\Facilities\Service\RelationalRepositoryProvider
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
            if (!isset($this->application->config->repositoryConfigs[$modelName])) {
                throw new InvalidConfigException(sprintf('repositoryConfigs has no model with name %s', $modelName));
            }
            $this->cache[$modelName] = new RelationalRepository(
                $this->application,
                $this->application->config->repositoryConfigs[$modelName]
            );
        }
        return $this->cache[$modelName];
    }
}
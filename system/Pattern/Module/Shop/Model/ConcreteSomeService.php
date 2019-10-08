<?php

namespace CodeHuiter\Pattern\Module\Shop\Model;

use CodeHuiter\Config\DateConfig;
use CodeHuiter\Service\ByDefault\Console;

class ConcreteSomeService implements SomeServiceInterface
{
    /**
     * @var Console
     */
    private $console;
    /**
     * @var DateConfig
     */
    private $dateConfig;

    public function __construct(Console $console, DateConfig $dateConfig)
    {
        $this->console = $console;
        $this->dateConfig = $dateConfig;
    }

    public function doSomething(): void
    {
        $this->console->log($this->dateConfig);

        echo 'Opapa';
    }
}
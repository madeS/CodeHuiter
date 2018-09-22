<?php

namespace CodeHuiter\Services;

use CodeHuiter\Core\Application;

class Queue
{
    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        //$this->lang = $app->get('lang');;
        $app->getConfig('queue');
    }

}

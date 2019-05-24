<?php

namespace CodeHuiter\Pattern\Module\Developing\Controller;

use CodeHuiter\Pattern\Controller\Base\BaseController;

class Main_Controller extends BaseController
{
    /**
     * @return bool|void
     */
    public function index()
    {
        echo "Developing index";
    }
}

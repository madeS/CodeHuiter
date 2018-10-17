<?php

namespace CodeHuiter\Pattern\Modules\Developing\Controllers;

use CodeHuiter\Pattern\Controllers\Base\BaseController;

class Test_Controller extends BaseController
{
    /**
     * @return bool|void
     */
    public function index()
    {
        echo "Developing test index";
    }

    public function some()
    {
        echo "Developing test some";
    }

}

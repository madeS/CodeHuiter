<?php

namespace App\Controller\Inner;

use CodeHuiter\Pattern\Controller\Base\BaseController;

class Test_Controller extends BaseController
{
    public function index()
    {
        //return $this->error404();
        echo 'INNER Is Test index function';
    }

    public function get($data1 = '', $data2 = '')
    {
        echo "INNER This is get method of test controller with data1 = $data1; and data2 = $data2";
    }
}

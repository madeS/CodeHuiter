<?php

namespace App\Controllers\Inner;

class Test_Controller extends \CodeHuiter\Pattern\Controllers\Base\BaseController
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

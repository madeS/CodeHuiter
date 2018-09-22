<?php

namespace App\Controllers;

class Test_Controller extends \CodeHuiter\Pattern\Controllers\Base\BaseController
{
    public function index()
    {
        //return $this->error404();
        //echo 'Is Test index function';
        phpinfo();
    }

    public function test($data1 = '', $data2 = '')
    {
        //echo "This is test method of test controller with data1 = $data1; and data2 = $data2";
        $this->data['test_var'] = $data1;
        $this->render(':pages/test');
    }

    public function get($data1 = '', $data2 = '')
    {
        echo "This is get method of test controller with data1 = $data1; and data2 = $data2";
    }
}

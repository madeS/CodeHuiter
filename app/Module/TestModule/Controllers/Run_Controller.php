<?php

namespace App\Module\TestModule\Controller;

class Run_Controller extends \CodeHuiter\Pattern\Controller\Base\BaseController
{
    public function index()
    {
        //return $this->error404();
        echo 'RUN Is Test index function';
    }

    public function test($data1 = '', $data2 = '')
    {
        //echo "This is test method of test controller with data1 = $data1; and data2 = $data2";
        $this->data['test_var'] = $data1;
        $this->render('test');
    }

    public function get($data1 = '', $data2 = '')
    {
        echo "RUN This is get method of test controller with data1 = $data1; and data2 = $data2";
    }
}

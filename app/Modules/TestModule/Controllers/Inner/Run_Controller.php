<?php

namespace App\Modules\TestModule\Controllers\Inner;

class Run_Controller extends \CodeHuiter\Pattern\Controllers\Base\BaseController
{
    public function index()
    {
        //return $this->error404();
        echo 'INNER RUN Is Test index function';
    }

    public function test($data1 = '', $data2 = '')
    {
        //echo "This is test method of test controller with data1 = $data1; and data2 = $data2";
        $this->data['test_var'] = $data1;
        $this->render('test');
    }

    public function get($data1 = '', $data2 = '')
    {
        echo "INNER RUN This is get method of test controller with data1 = $data1; and data2 = $data2";
    }
}

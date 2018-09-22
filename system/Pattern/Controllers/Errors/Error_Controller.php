<?php

namespace CodeHuiter\Pattern\Controllers\Errors;

use CodeHuiter\Core\Controller;

class Error_Controller extends Controller
{
    public function error404()
    {
        echo 'This is error 404';
    }

    public function error500($exception)
    {
        if ($exception instanceof \Exception) {

            echo 'Error 500 with Exception';

            print_r($exception);

        } else {
            echo 'Error 500 without exception';
        }
    }
}

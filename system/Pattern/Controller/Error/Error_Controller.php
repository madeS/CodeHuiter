<?php

namespace CodeHuiter\Pattern\Controller\Error;

use CodeHuiter\Core\Controller;

class Error_Controller extends Controller
{
    public function error403($message)
    {
        echo 'This is error 403';

        echo '<br/><br/>Additional Message: ' . $message;
    }

    public function error404($message)
    {
        echo 'This is error 404';

        echo '<br/><br/>Additional Message: ' . $message;
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

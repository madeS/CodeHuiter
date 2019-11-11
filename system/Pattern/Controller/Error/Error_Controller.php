<?php

namespace CodeHuiter\Pattern\Controller\Error;

use CodeHuiter\Core\Controller;

class Error_Controller extends Controller
{
    public function error403($message)
    {
        $this->response->setStatus(403);
        $this->response->append('This is error 403');
        $this->response->append('<br/><br/>Additional Message: ' . $message);
    }

    public function error404($message)
    {
        $this->response->setStatus(404);
        $this->response->append('This is error 404');
        $this->response->append('<br/><br/>Additional Message: ' . $message);
    }

    public function error500($exception)
    {
        $this->response->setStatus(500);
        if ($exception instanceof \Exception) {

            $this->response->append('Error 500 with Exception');

            print_r($exception);

        } else {
            $this->response->append('Error 500 without exception');
        }
    }
}

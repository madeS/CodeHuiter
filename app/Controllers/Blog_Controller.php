<?php

namespace App\Controllers;

class Blog_Controller extends DefaultController
{
    public function index()
    {
        $this->initWithAuth(false);

        //$this->render(':pages/blog');
        $this->render(SYSTEM_PATH . 'Pattern/Views/' . 'pages/blog');
    }
}

<?php

namespace App\Controller;

class Blog_Controller extends DefaultController
{
    public function index(): void
    {
        $this->initWithAuth(false);

        //$this->render(':pages/blog');
        $this->render(SYSTEM_PATH . 'Pattern/View/' . 'pages/blog');
    }
}

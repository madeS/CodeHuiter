<?php

namespace App\Controller;

use CodeHuiter\Core\CodeLoader;

class Blog_Controller extends DefaultController
{
    public function index(): void
    {
        $this->initWithAuth(false);
        //$this->render(':pages/blog');
        $this->render(SYSTEM_PATH . 'Facilities/View/' . 'pages/blog');
    }
}

<?php

namespace CodeHuiter\Pattern\Module\Developing\Controller;

use CodeHuiter\Exception\ErrorException;
use CodeHuiter\Pattern\Controller\Base\BaseController;

class Language_Controller extends BaseController
{
    /**
     * @return bool|void
     */
    public function index()
    {
        echo "Developing test index";
    }

    /**
     * @throws ErrorException
     */
    public function generate()
    {
        if (!$this->request->isCli()) {
            throw new ErrorException('This method runs only by CLI');
        }
        echo "Developing test some";
    }

}

<?php

namespace CodeHuiter\Facilities\Module\Developing\Controller;

use CodeHuiter\Exception\ErrorException;
use CodeHuiter\Facilities\Controller\Base\BaseController;

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

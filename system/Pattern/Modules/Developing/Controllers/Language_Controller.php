<?php

namespace CodeHuiter\Pattern\Modules\Developing\Controllers;

use CodeHuiter\Exceptions\ErrorException;
use CodeHuiter\Pattern\Controllers\Base\BaseController;

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

<?php

namespace App\Controller;

use CodeHuiter\Pattern\Controller\Base\BaseController;

class Test_Controller extends BaseController
{
    public function index()
    {
        //return $this->error404();
        //echo 'Is Test index function';
        phpinfo();
    }

    public function bench($data1 = '', $data2 = '')
    {
        //echo "This is test method of test controller with data1 = $data1; and data2 = $data2";
        $this->data['test_var'] = $data1;
        $this->render(':pages/test');
    }

    public function get($data1 = '', $data2 = '')
    {
        echo "This is get method of test controller with data1 = $data1; and data2 = $data2";
    }

    public function prop()
    {
        $prop = 0.04;
        $total = 1;
        $fail = 1;

        $totalCount = 0;
        $totalFailed = 0;
        while (true) {
            $iterationFailed = 0;
            for ($i = 0; $i < $total; $i++) {
                if($this->getRandomResultWithProp($prop)) {
                    $iterationFailed++;
                }
            }

            if ($iterationFailed >= $fail) {
                $totalFailed++;
            }
            $totalCount++;

            $failedNum = number_format(($totalFailed/$totalCount) * 100, 3);
            $this->console->log("Result: $failedNum % ... (for $totalCount)", true, false);
        }
    }

    private function getRandomResultWithProp(float $prop): bool
    {
        $max = 100;
        $rand = random_int(0, $max - 1);
        if ($rand/$max < $prop) {
            return true;
        }
        return false;
    }

}

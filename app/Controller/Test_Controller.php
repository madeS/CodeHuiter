<?php

namespace App\Controller;

use CodeHuiter\Database\RelationalModelRepository;
use CodeHuiter\Pattern\Controller\Base\BaseController;
use CodeHuiter\Pattern\Module\Shop\Model\ShopCategoryProductModel;
use CodeHuiter\Pattern\Module\Shop\Model\SomeServiceInterface;

class Test_Controller extends BaseController
{
    public function index()
    {
        //return $this->error404();
        //echo 'Is Test index function';
        phpinfo();
    }


    public function testPartModel(): void
    {
        /** @var ShopCategoryProductModel $model */
        $model = ShopCategoryProductModel::getEmpty();
        $model->setCreatedAt($this->date->sqlTime());
        $model->setOnePrimaryField(555);

        $repository = new RelationalModelRepository($this->app, new ShopCategoryProductModel());
        $repository->save($model);

        echo $model;

        $this->debug->outToHtml($this->app->config->services);

        $this->console->log('Its work yesss');

        /** @var SomeServiceInterface $someService */
        $someService = $this->app->get(SomeServiceInterface::class);
        $someService->doSomething();

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

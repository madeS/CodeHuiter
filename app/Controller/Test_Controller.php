<?php

namespace App\Controller;

use CodeHuiter\Config\Database\DatabaseConfig;
use CodeHuiter\Database\RelationalDatabaseHandler;
use CodeHuiter\Facilities\Controller\Base\BaseController;

class Test_Controller extends BaseController
{
    public function index()
    {
        //return $this->error404();
        //echo 'Is Test index function';
        /** @noinspection ForgottenDebugOutputInspection */
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


    public function migrate(): void
    {
        $db = $this->getDb();

        $medias = $db->select("SELECT * FROM user_medias WHERE 1");
        foreach ($medias as $media) {
            $db->execute(
                "UPDATE user_medias SET created_at = :time WHERE id = :id",
                [
                    ':time' => $this->date->sqlTime($media['created_at_int']),
                    ':id' => $media['id'],
                ]
            );
            $db->execute(
                "UPDATE user_medias SET updated_at = :time WHERE id = :id",
                [
                    ':time' => $this->date->sqlTime($media['updated_at_int']),
                    ':id' => $media['id'],
                ]
            );
        }

        $medias = $db->select("SELECT * FROM user_albums WHERE 1");
        foreach ($medias as $media) {
            $db->execute(
                "UPDATE user_albums SET created_at = :time WHERE id = :id",
                [
                    ':time' => $this->date->sqlTime($media['created_at_int']),
                    ':id' => $media['id'],
                ]
            );
            $db->execute(
                "UPDATE user_albums SET updated_at = :time WHERE id = :id",
                [
                    ':time' => $this->date->sqlTime($media['updated_at_int']),
                    ':id' => $media['id'],
                ]
            );
            $db->execute(
                "UPDATE user_albums SET show_at = :time WHERE id = :id",
                [
                    ':time' => $this->date->sqlTime($media['date_show']),
                    ':id' => $media['id'],
                ]
            );
        }
    }

    private function getDb(): RelationalDatabaseHandler
    {
        return $this->app->get(DatabaseConfig::SERVICE_DB_DEFAULT);
    }
}

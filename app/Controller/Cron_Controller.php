<?php

namespace App\Controller;

use CodeHuiter\Pattern\Controller\Base\BaseController;

class Cron_Controller extends BaseController
{
    public function index()
    {
        //return $this->error404();
        echo 'Is Test index function';
    }

    public function cron_every_minute(){
        $cronTimes = explode('-',$this->mm->date('Y-m-d-H-i-s-w-W', array(), array(
            'timezone' => -180 // Минское время крона
        )));
        $cronTime = array(
            'year' => intval($cronTimes[0]),
            'month' => intval($cronTimes[1]),
            'day' => intval($cronTimes[2]),
            'hour' => intval($cronTimes[3]),
            'minute' => intval($cronTimes[4]),
            'second' => intval($cronTimes[5]),
            'weekday' => intval($cronTimes[6]),// 0 sunday, 1 monday .. 6 sat.
            'week' => intval($cronTimes[7]),// week in year
        );
        $this->everyMinute();
        if ($cronTime['minute'] === 7) {
            // Каждый час на 7ой минуте
            $this->everyHour();
        }
        if ($cronTime['hour'] === 4 && $cronTime['minute'] === 20) {
            // Каждый день в 4:20
            $this->everyDay();
        }
        if ($cronTime['weekday'] === 0 && $cronTime['hour'] === 3 && $cronTime['minute'] === 30) {
            // Каждую неделю в ВС в 3:30
            $this->everyWeek();
        }
        if ($cronTime['day'] === 1 && $cronTime['hour'] === 2 && $cronTime['minute'] === 40) {
            // Каждый месяц 1ого числа в 2:40
            $this->everyMonth();
        }
    }

    protected function everyMinute()
    {

    }

    protected function everyHour()
    {

    }

    protected function everyDay()
    {

    }

    protected function everyWeek()
    {

    }

    protected function everyMonth()
    {

    }

    public function test()
    {
        echo 'This is a cron test';
    }

}

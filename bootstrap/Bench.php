<?php

namespace Bootstrap;

class Bench
{
    public function run($params = [])
    {
        $count = $params[1];
        unset($params[0]);
        unset($params[1]);
        $params = implode (' ', $params);
        $params = $params ? ' ' . $params : $params;

        $indicateStep = $count / 20;

        /**
         * Show almost empty script result
         */
        $startTime = microtime(true);
        for ($i = 0; $i < $indicateStep; $i++) {
            $output = shell_exec('php cron.php -m1' . $params);
        }
        $temp = number_format( (microtime(true) - $startTime) / $indicateStep, 3);
        echo "SCRIPT_START preliminary request time: {$temp}\n";

        /**
         * Show Framework init result
         */
        $startTime = microtime(true);
        for ($i = 0; $i < $indicateStep; $i++) {
            $output = shell_exec('php cron.php -m2' . $params);
        }
        $temp = number_format( (microtime(true) - $startTime) / $indicateStep, 3);
        echo "APP_INIT preliminary request time: {$temp}\n";

        /**
         * Show Framework init result
         */
        $startTime = microtime(true);
        for ($i = 0; $i < $indicateStep; $i++) {
            $output = shell_exec('php cron.php test bench');
        }
        $temp = number_format( (microtime(true) - $startTime) / $indicateStep, 3);
        echo "APP_BENCH preliminary request time: {$temp}\n";

        /**
         * Main benchmark
         */
        $preliminaryResult = null;
        $startTime = microtime(true);
        for ($i = 0; $i < $count; $i++) {
            if ($i % $indicateStep === 0 && $i !== 0) {
                if ($preliminaryResult === null) {
                    $preliminaryResult = number_format( (microtime(true) - $startTime) / $indicateStep, 3);
                    echo "TEST Preliminary request time: {$preliminaryResult}\n";
                }
                echo '.';
            }
            $output = shell_exec('php cron.php' . $params);
        }
        $endTime = microtime(true);

        $result = number_format(($endTime - $startTime) / $count, 3);
        echo "\n";
        echo "Avg TEST request time: {$result}\n";
    }

}

$bench = new Bench();
$bench->run($argv);

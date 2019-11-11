<?php

class SimpleBench
{
    private $startTime;

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    public function end(): void
    {
        $bTimeEnd = microtime(true);
        $bElapsedOrig = $bTimeEnd - $this->startTime;
        $bElapsed = number_format($bTimeEnd - $this->startTime, 6);
        echo "elapsed: $bElapsed;";
        echo " Memory: " . number_format(memory_get_usage(false) / 1024, 2) . 'KB;';
        echo ' Real: ' . number_format(memory_get_usage(true) / 1024, 2) . 'KB;';
        $classes = $this->getLoadedClasses();
        $countClasses = count($classes);
        echo " Classes loaded $countClasses;";

        $file = sys_get_temp_dir() . '/' . preg_replace('/[^A-Za-z0-9_-]/ui', '', ($_SERVER['HTTP_HOST'] ?? '') . '_' . ($_SERVER['REQUEST_URI'] ?? '')) . '.txt';
        $times = [];
        if (file_exists($file)) {
            $fileContent = file_get_contents($file);
            $strings = explode("\n", $fileContent);
            foreach ($strings as $string) {
                $stringData = explode(';', $string);
                if (count($stringData) === 1) continue;
                if (intval($stringData[0]) < time() - 60) continue;
                $times[$stringData[0]] = $stringData[1];
            }
        }
        $times[time() . '_' . rand(1000,9999)] = $bElapsedOrig;
        $avgTime = array_sum($times) / count($times);
        echo ' Avg(' . count($times) . '): ' . number_format($avgTime, 6);

        echo '<pre>' . print_r($classes, true) . '</pre>';

        $fileContent = '';
        foreach ($times as $key => $value)
        {
            $fileContent .= "$key;$value\n";
        }
        file_put_contents($file, $fileContent);
    }

    private function getLoadedClasses(): array
    {
        $allClasses = get_declared_classes();
        $result = [];
        $thisClassKey = array_search('SimpleBench', $allClasses, true);
        if ($thisClassKey > 0) {
            $totalAllClassesCount = count($allClasses);
            for ($i = $thisClassKey; $i < $totalAllClassesCount; $i++) {
                $result[] = $allClasses[$i];
            }
        }
        return $result;
    }
}
return new SimpleBench();

// $simpleBench = require __DIR__ . '/SimpleBench.php';
// Code
// $simpleBench->end();



<?php

//$bTimeStart = microtime(true);
//Code
//require_once __DIR__ . '/' . 'SimpleBench.php';

$bTimeEnd = microtime(true);
$bElapsedOrig = $bTimeEnd - $bTimeStart;
$bElapsed = number_format($bTimeEnd - $bTimeStart, 6);
echo "elapsed: $bElapsed; Memory: " . number_format(memory_get_usage(false) / 1024, 2) . 'KB' . ' Real: ' . number_format(memory_get_usage(true) / 1024, 2) . 'KB';


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

$fileContent = '';
foreach ($times as $key => $value)
{
    $fileContent .= "$key;$value\n";
}
file_put_contents($file, $fileContent);


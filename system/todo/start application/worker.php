<?php

$currentDir = __DIR__;
$batFile = $currentDir . '\\' . 'start_page.bat';
echo $batFile;

while(true) {
	sleep(10);
	system("cmd /c $batFile");

}
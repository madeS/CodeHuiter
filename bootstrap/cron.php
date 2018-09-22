<?php
/* стандартные ограничения нам не подходят. ставим свои */
set_time_limit(0);
ini_set('memory_limit', '256M');
 
/* проверочка. чтобы этот скрипт по неосторожности никто не вызвал из браузера */
if (isset($_SERVER['REMOTE_ADDR'])) die('Permission denied.');

/*  вручную подменяем путь URI на основе параметров командной строки */
unset($argv[0]); /* первый параметр нам ни к чему, это имя скрипта */
$_SERVER['PATH_INFO'] = $_SERVER['REQUEST_URI'] = '/' . implode('/', $argv) ;
 
/* подключаем framework */
require __DIR__ . '/../bootstrap/loader.php';
 
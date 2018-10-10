<?php

chdir(dirname(__FILE__));
define('BASE_PATH', realpath('./..') . '/');
define('SYSTEM_PATH', realpath('./../system') . '/');
define('PUB_PATH', realpath('./../public_html') . '/');
define('APP_PATH', realpath('./../app') . '/');
define('VIEW_PATH', APP_PATH . 'Views/');

require SYSTEM_PATH . 'Core/Benchmark.php';
$benchmark = new \CodeHuiter\Core\Benchmark();

if ($benchmark->benchMode == \CodeHuiter\Core\Benchmark::BENCH_MODE_SCRIPT_START) exit(0);

/*
header("Content-Type: text/html; charset=utf-8");

//$_GET['codeerror'] = 503;
if (isset($_GET['setdev']) && $_GET['setdev']==='ok'){
	setcookie('developer',1);
	exit();
}

if (isset($_GET['codeerror']) && !isset($_COOKIE['developer'])){
	if (false || $_GET['codeerror'] == 503){
		$protocol = 'HTTP/1.0';
		header($protocol.' 503 Service Unavailable');
		header('Retry-After: 1800');
		
		$docroot = (isset($_SERVER['DOCUMENT_ROOT'])) ? rtrim($_SERVER['DOCUMENT_ROOT'],'/') . '/' : '';
		$tpl503 = '/application/views/mop/page_503.tpl.php';
		if (file_exists($docroot . $tpl503)){
			require $docroot . $tpl503;
		}
		exit();
	}
}
*/

/**
 * Using The Composer AutoLoader
 */
$composerAutoloader = require __DIR__.'/../vendor/autoload.php';
$benchmark->setAutoloader($composerAutoloader);

/*
|--------------------------------------------------------------------------
| Include The Compiled Class File
|--------------------------------------------------------------------------
|
| To dramatically increase your application's performance, you may use a
| compiled class file which contains all of the classes commonly used
| by a request. The Artisan "optimize" is used to create this file.
|
*/

$compiledPath = __DIR__.'/cache/compiled.php';

if (file_exists($compiledPath)) {
    require $compiledPath;
}

/**
 * @TODO move these functions to specialazed classes
 */
require_once(SYSTEM_PATH.'Core/Common.php');

// Define a custom error handler so we can log PHP errors
set_error_handler('_error_handler');
set_exception_handler('_exception_handler');
register_shutdown_function('_shutdown_handler');
// --------------------------------------------------

$app = \CodeHuiter\Core\Application::getInstance();
$app->set(\CodeHuiter\Config\Config::SERVICE_KEY_BENCHMARK, $benchmark);
if ($benchmark->benchMode == \CodeHuiter\Core\Benchmark::BENCH_MODE_APP_INIT) exit(0);
$app->run();

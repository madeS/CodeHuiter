<?php

chdir(__DIR__);
define('BASE_PATH', dirname(__DIR__) . '/');
define('SYSTEM_PATH', BASE_PATH . 'system/');
define('PUB_PATH', BASE_PATH . 'public_html/');
define('APP_PATH', BASE_PATH . 'app/');
define('STORAGE_PATH', BASE_PATH . 'storage/');
define('VIEW_PATH', APP_PATH . 'View/');
define('CACHE_PATH', STORAGE_PATH . 'framework/cache/');

require SYSTEM_PATH . 'Core/CodeLoader.php';
$codeLoader = new \CodeHuiter\Core\CodeLoader();
if ($_GET['debug_bench'] ?? null) {
    $codeLoader->setBenchMode(\CodeHuiter\Core\CodeLoader::BENCH_MODE_BENCH_TIMES_AND_MEMORY);
}

/*
maintenance
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
		$tpl503 = '/application/View/mop/page_503.tpl.php';
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
$codeLoader->setAutoloader($composerAutoloader);

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
$app->set(\CodeHuiter\Core\CodeLoader::class, $codeLoader);
$app->run();

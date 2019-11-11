<?php

chdir(__DIR__);
define('BASE_PATH', dirname(__DIR__) . '/');
define('SYSTEM_PATH', BASE_PATH . 'system/');
define('PUB_PATH', BASE_PATH . 'public_html/');
define('APP_PATH', BASE_PATH . 'app/');
define('STORAGE_PATH', BASE_PATH . 'storage/');
define('STORAGE_TEMP_PATH', STORAGE_PATH . 'temp/');
define('STORAGE_PUB_PATH', PUB_PATH . 'pub/files/');
define('VIEW_PATH', APP_PATH . 'View/');
define('CACHE_PATH', STORAGE_PATH . 'framework/cache/');

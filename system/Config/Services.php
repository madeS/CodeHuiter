<?php

return [
    // Core Services
    'benchmark' => ['single' => true, 'class' => '\\CodeHuiter\\Core\\Benchmark'],
    'db' => ['single' => true, 'callback' => function(\CodeHuiter\Core\Application $app) {
        return new \CodeHuiter\Database\Drivers\PDOMySQL(
            $app->get('log'), $app->getConfig('database_default')
        );
    }],
    'config' => ['single' => true, 'callback' => function(\CodeHuiter\Core\Application $app) {
        $environment = $app->getEnvironment();
        $configClassName = "\\App\\Config\\{$environment}Config";
        return new $configClassName();
    }],
    'router' => ['single' => true, 'class_app' => '\\CodeHuiter\\Core\\Router'],
    'response' => ['single' => true, 'class_app' => '\\CodeHuiter\\Core\\Response'],
    'request' => ['single' => true, 'class_app' => '\\CodeHuiter\\Core\\Request'],
    'log' => ['single' => true, 'class_app' => '\\CodeHuiter\\Services\\Log\\Log'],

    'mime_types' => ['single' => true, 'class' => '\\CodeHuiter\\Config\\Data\\MimeTypes'],
    'email' => ['single' => true, 'class_app' => '\\CodeHuiter\\Services\\Email\\Mailer\\Mailer'],

    'lang' => ['single' => true, 'class' => '\\CodeHuiter\\Services\\Language'],
    'date' => ['single' => true, 'class_app' => '\\CodeHuiter\\Services\\DateService'],
    'mjsa' => ['single' => true, 'class_app' => '\\CodeHuiter\\Services\\Mjsa'],

    // Framework Services
    'compressor' => ['single' => true, 'class_app' => '\\CodeHuiter\\Services\\Compressor'],

    'links' => ['single' => true, 'class' => '\\App\\Services\\Links'],

    'auth' => ['single' => true, 'class_app' => '\\CodeHuiter\\Pattern\\Modules\\Auth\\AuthService'],
];
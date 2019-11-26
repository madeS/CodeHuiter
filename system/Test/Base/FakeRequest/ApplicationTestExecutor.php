<?php

namespace CodeHuiter\Test\Base\FakeRequest;

use CodeHuiter\Config\Config;
use CodeHuiter\Config\ConfigTest;
use CodeHuiter\Core\Application;
use CodeHuiter\Core\ByDefault\Request;
use CodeHuiter\Core\Response;

class ApplicationTestExecutor
{
    /**
     * @var ApplicationTestExecutor|null
     */
    private static $testApplication;

    /**
     * @var Application
     */
    private $application;

    public static function getInstance(): ApplicationTestExecutor
    {
        if (self::$testApplication === null) {
            require __DIR__ . '/../../../../bootstrap/paths.php';
            $application = Application::getInstance();
            $originConfig = $application->config;
            $application->init(ConfigTest::class);
            $testConfig = $application->config;
            self::$testApplication = new self($application);

            self::dumpOriginDb($originConfig);
            self::loadTestDb($testConfig);
        }
        return self::$testApplication;
    }

    private function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function runWithGetRequest(string $uri, array $cookie = []): ?Response
    {
        $request = new Request(
            $this->application->config->requestConfig,
            [
                INPUT_SERVER => [
                    //'REQUEST_METHOD' => ''
                    'HTTP_HOST' => $this->application->config->settingsConfig->domain,
                    'REQUEST_URI' => $uri
                ],
                INPUT_COOKIE => $cookie,
            ]
        );

        return $this->application->run($request);
    }

    public function runWithPostRequest(string $uri, array $data, array $cookie = []): ?Response
    {
        $request = new Request(
            $this->application->config->requestConfig,
            [
                INPUT_SERVER => [
                    //'REQUEST_METHOD' => ''
                    'HTTP_HOST' => $this->application->config->settingsConfig->domain,
                    'REQUEST_URI' => $uri
                ],
                INPUT_POST => $data,
                INPUT_COOKIE => $cookie,
            ]
        );

        return $this->application->run($request);
    }


    private static function dumpOriginDb(Config $originConfig): void
    {
        $host = self::getDsnAttribute('host', $originConfig->defaultDatabaseConfig->dsn);
        $database = self::getDsnAttribute('dbname', $originConfig->defaultDatabaseConfig->dsn);
        $charset = self::getDsnAttribute('charset', $originConfig->defaultDatabaseConfig->dsn) ?? $originConfig->defaultDatabaseConfig->charset;
        $user = $originConfig->defaultDatabaseConfig->username;
        $password = $originConfig->defaultDatabaseConfig->password;
        $filename = STORAGE_TEMP_PATH . 'originDb.dump.sql';

        exec(
            "export MYSQL_PWD=$password ;mysqldump -h $host -u $user $database --add-drop-table --skip-add-locks --default-character-set=$charset --single-transaction > $filename",
            $output,
            $returnVar
        );
    }

    private static function loadTestDb(Config $testConfig): void
    {
        $host = self::getDsnAttribute('host', $testConfig->defaultDatabaseConfig->dsn);
        $database = self::getDsnAttribute('dbname', $testConfig->defaultDatabaseConfig->dsn);
        $user = $testConfig->defaultDatabaseConfig->username;
        $password = $testConfig->defaultDatabaseConfig->password;
        $filename = STORAGE_TEMP_PATH . 'originDb.dump.sql';

        exec(
//            "mysql -h $host -u $user -p$password -e \"
//                    DROP DATABASE IF EXISTS $database;
//                    CREATE database $database;
//                    USE $database;
//                    SOURCE $filename;
//            \"",
            "export MYSQL_PWD=$password ;mysql -h $host -u $user -e \"
                    USE $database; 
                    SOURCE $filename;
            \"",
            $output,
            $returnVar
        );
    }

    /**
     * @param string $name
     * @param string $dsn
     * @return string|null
     */
    private static function getDsnAttribute(string $name, string $dsn): ?string
    {
        if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        }
        return null;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }
}


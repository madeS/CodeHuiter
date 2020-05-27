<?php

namespace CodeHuiter\FunctionalTest\Base\FakeRequest;

use CodeHuiter\Config\TestFacilitiesConfig;
use CodeHuiter\Core\Application;
use CodeHuiter\Core\ByDefault\Request;
use CodeHuiter\Core\Response;
use CodeHuiter\Facilities\Module\Developing\DevelopingService;

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
            $application->init(TestFacilitiesConfig::class);
            $testConfig = $application->config;
            self::$testApplication = new self($application);

            $databaseManager = self::$testApplication->getDevelopingService()->getDatabaseManager();
            $databaseManager->saveDumpDB($originConfig->defaultDatabaseConfig, STORAGE_TEMP_PATH, 'originForTest');
            $databaseManager->loadDumpDB($testConfig->defaultDatabaseConfig, STORAGE_TEMP_PATH, 'originForTest');
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

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function getDevelopingService(): DevelopingService
    {
        return $this->getApplication()->get(DevelopingService::class);
    }
}


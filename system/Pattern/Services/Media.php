<?php

namespace CodeHuiter\Pattern\Services;

use CodeHuiter\Config\MediaConfig;
use CodeHuiter\Core\Application;

class Media
{
    /**
     * @var MediaConfig
     */
    private $config;

    public function __construct(Application $application)
    {
        $this->config = $application->config->mediaConfig;
    }

    /**
     * Возвращает Серверный путь до контента
     *
     * @param string $content Тип контента
     * @param string $path Остаточный путь до контента
     * @return string
     */
    protected function serverStore($content, $path){
        return $this->config->storageMap[$content]['server_root']
            . $this->config->storageMap[$content]['store']
            . $path;
    }

    /**
     * Возвращает HTTP путь до контента
     *
     * @param string $content Тип контента
     * @param string $path Остаточный путь до контента
     * @return string
     */
    public function store($content, $path){
        return $this->config->storageMap[$content]['site_url']
            . $this->config->storageMap[$content]['store']
            . $path;

    }

    public function upload($fromFile, $content, $path)
    {

    }

    public function download($content, $path, $toFile)
    {

    }

    public function delete()
    {

    }
}

<?php

namespace CodeHuiter\Pattern\Service;

use CodeHuiter\Config\MediaConfig;

class Media
{
    /**
     * @var MediaConfig
     */
    private $config;

    public function __construct(MediaConfig $mediaConfig)
    {
        $this->config = $mediaConfig;
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

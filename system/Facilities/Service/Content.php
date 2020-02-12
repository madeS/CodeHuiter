<?php

namespace CodeHuiter\Facilities\Service;

use CodeHuiter\Config\ContentConfig;
use CodeHuiter\Service\FileStorage;
use CodeHuiter\Service\Logger;

class Content
{
    /**
     * @var ContentConfig
     */
    private $config;

    /**
     * @var FileStorage
     */
    private $fileStorage;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        ContentConfig $mediaConfig,
        FileStorage $fileStorage,
        Logger $logger
    ) {
        $this->config = $mediaConfig;
        $this->fileStorage = $fileStorage;
        $this->logger = $logger;
    }

    /**
     * Возвращает Серверный путь до контента
     *
     * @param string $content Тип контента
     * @param string $path Остаточный путь до контента
     * @return string
     */
    public function serverStore($content, $path){
        return rtrim($this->config->storageMap[$content]['server_root'], '/')
            . $this->config->storageMap[$content]['store']
            . $path;
    }

    /**
     * Возвращает HTTP путь до контента
     *
     * @see ContentConfig::$storageMap
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

    public function delete($content, $path)
    {
        $file = $this->serverStore($content, $path);
        $this->fileStorage->deleteFile($file);
    }
}

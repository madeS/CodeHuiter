<?php

namespace CodeHuiter\Pattern\Services;

use CodeHuiter\Config\PatternConfig;
use CodeHuiter\Core\Application;

class Media
{
    /**
     * @var array
     */
    private $config;

    public function __construct(Application $application)
    {
        $this->config = $application->getConfig(PatternConfig::CONFIG_KEY_MEDIA);
    }

    /** serverStore
     * Возвращает Серверный путь до контента
     *
     * @param string $content Тип контента
     * @param string $path Остаточный путь до контента
     * @return string
     */
    public function serverStore($content, $path){
        return $this->config['storage_map'][$content]['server_root']
            . $this->config['storage_map'][$content]['store']
            . $path;
    }

    /** store
     * Возвращает HTTP путь до контента
     *
     * @param string $content Тип контента
     * @param string $path Остаточный путь до контента
     * @return string
     */
    public function store($content, $path){
        return $this->config['storage_map'][$content]['site_url']
            . $this->config['storage_map'][$content]['store']
            . $path;

    }
}

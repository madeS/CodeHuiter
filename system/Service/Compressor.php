<?php

namespace CodeHuiter\Service;

use CodeHuiter\Config\CompressorConfig;
use CodeHuiter\Config\Config;
use CodeHuiter\Core\Application;
use CodeHuiter\Core\Request;
use CodeHuiter\Core\Response;

class Compressor
{
    /** @var CompressorConfig  */
    protected $config;

    /** @var Request $request */
    protected $request;

    /** @var Response $response */
    protected $response;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->config = $app->config->compressorConfig;
        $this->request = $app->get(Config::SERVICE_KEY_REQUEST);
        $this->response = $app->get(Config::SERVICE_KEY_RESPONSE);
    }

    /**
     * @return CompressorConfig
     */
    public function checkCompress()
    {
        if (!empty($this->config->domainCompressor[$this->request->domain])) {
            $this->config = $this->config->domainCompressor[$this->request->domain];
        }

        $outputFileTemplate = $this->config->dir . '/' . $this->config->names . '_' . $this->config->version;
        $exts = ['css' => 'resultCss','js' => 'resultJs'];
        foreach ($exts as $ext => $resultProp) {
            $outputFile = PUB_PATH . $outputFileTemplate . '.' . $ext;
            if (!file_exists($outputFile) || $this->config->version === 'dev') {
                $fp = fopen($outputFile, 'w');
                foreach ($this->config->$ext as $connected) {
                    $connectedExtArr = explode('.',$connected);
                    if (end($connectedExtArr) === 'php') {
                        $connected_content = $this->response->render(PUB_PATH . $connected, [],true);
                    } else {
                        $connected_content = file_get_contents(PUB_PATH . $connected);
                    }
                    if (true) { // remove comments
                        $connected_content = preg_replace('!/\*.*?\*/!s', '', $connected_content);
                        $connected_content = preg_replace('/\n\s*\n/', "\n", $connected_content);
                    }
                    fwrite($fp, "/* $connected */ \n" . $connected_content ."\n");
                }
                fclose($fp);
            }

            $this->config->$resultProp = $outputFileTemplate . '.' . $ext;
            if ($this->config->version === 'dev'){
                $this->config->$resultProp .= '?t='.time();
            }
        }
        return $this->config;
    }
}

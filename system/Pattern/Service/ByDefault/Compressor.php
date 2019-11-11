<?php

namespace CodeHuiter\Pattern\Service\ByDefault;

use CodeHuiter\Config\CompressorConfig;
use CodeHuiter\Core\Request;
use CodeHuiter\Service\ByDefault\PhpRenderer;

class Compressor implements \CodeHuiter\Pattern\Service\Compressor
{
    /** @var CompressorConfig  */
    protected $config;

    /** @var Request $request */
    protected $request;

    /** @var PhpRenderer $renderer */
    protected $renderer;

    /**
     * @param CompressorConfig $config
     * @param Request $request
     * @param PhpRenderer $renderer
     */
    public function __construct(CompressorConfig $config, Request $request, PhpRenderer $renderer)
    {
        $this->config = $config;
        $this->request = $request;
        $this->renderer = $renderer;
    }

    /**
     * @return CompressorConfig
     */
    public function checkCompress(): CompressorConfig
    {
        if (!empty($this->config->domainCompressor[$this->request->getDomain()])) {
            $this->config = $this->config->domainCompressor[$this->request->getDomain()];
        }

        $outputFileTemplate = $this->config->dir . '/' . $this->config->names . '_' . $this->config->version;
        $exts = ['css' => 'resultCss','js' => 'resultJs'];
        foreach ($exts as $ext => $resultProp) {
            $outputFile = PUB_PATH . $outputFileTemplate . '.' . $ext;
            if ($this->config->version === 'dev' || !file_exists($outputFile)) {
                $fp = fopen($outputFile, 'wb');
                foreach ($this->config->$ext as $connected) {
                    $connectedExtArr = explode('.',$connected);
                    if (end($connectedExtArr) === 'php') {
                        $connected_content = $this->renderer->render(PUB_PATH . $connected, [],true);
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

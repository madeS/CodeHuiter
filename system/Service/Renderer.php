<?php

namespace CodeHuiter\Service;

interface Renderer
{
    /**
     * @param $viewFile
     * @param array $data
     * @param bool $return
     * @return string
     */
    public function render(string $viewFile, array $data = [], $return = false): string;
}

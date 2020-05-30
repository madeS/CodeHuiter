<?php

namespace CodeHuiter\Facilities\Service;

use CodeHuiter\Config\Facilities\Service\CompressorConfig;

interface Compressor
{
    /**
     * @return CompressorConfig Updated Compressor Config
     */
    public function checkCompress(): CompressorConfig;
}

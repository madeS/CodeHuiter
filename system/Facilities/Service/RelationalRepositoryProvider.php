<?php

namespace CodeHuiter\Facilities\Service;

use CodeHuiter\Database\RelationalRepository;

interface RelationalRepositoryProvider
{
    public function get(string $modelName): RelationalRepository;
}

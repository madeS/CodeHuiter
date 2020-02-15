<?php

namespace App\Module\ChromeExtension\Model;

use CodeHuiter\Database\RelationalModel;

class YoutubeCacheModel extends RelationalModel
{
    protected $_table = 'youtube_api_cache';
    protected $_databaseServiceKey = 'db';
    protected $_primaryFields = ['id'];
    protected $_autoIncrementField = 'id';

    /** @var string */
    public $id;
    /** @var string */
    public $data;
    /** @var string */
    public $created_at;
    /** @var string */
    public $updated_at;
}

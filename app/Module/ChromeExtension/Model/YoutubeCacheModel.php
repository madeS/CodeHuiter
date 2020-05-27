<?php

namespace App\Module\ChromeExtension\Model;

use CodeHuiter\Database\Model;

class YoutubeCacheModel extends Model
{
    /** @var string */
    public $id;
    /** @var string */
    public $data;
    /** @var string */
    public $created_at;
    /** @var string */
    public $updated_at;
}

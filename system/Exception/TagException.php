<?php
namespace CodeHuiter\Exception;

use Throwable;

class TagException extends CodeHuiterException
{
    protected $tag = '';

    public function __construct($tag, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->tag = $tag;
        parent::__construct($message, $code, $previous);
    }

    public function getTag()
    {
        return $this->tag;
    }
}

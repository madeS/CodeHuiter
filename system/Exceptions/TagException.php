<?php
namespace CodeHuiter\Exceptions;

use Throwable;

class TagException extends CodeHuiterException
{
    protected $tag = '';

    public function __construct(string $tag, string $message = "", int $code = 0, Throwable $previous = null)
    {
        $this->tag = $tag;
        parent::__construct($message, $code, $previous);
    }

    public function getTag()
    {
        return $this->tag;
    }
}
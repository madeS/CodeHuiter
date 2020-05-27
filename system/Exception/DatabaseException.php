<?php
namespace CodeHuiter\Exception;

use CodeHuiter\Config\RelationalDatabaseConfig;
use PDOException;
use Throwable;

class DatabaseException extends CodeHuiterRuntimeException
{
    public function __construct(string $message = '', ?Throwable $previous = null)
    {
        parent::__construct($message, 500, $previous);
    }

    public static function onPDOConnect(PDOException $exception, RelationalDatabaseConfig $config): self
    {
        return new self($exception->getMessage() . print_r($config, true), $exception);
    }
}

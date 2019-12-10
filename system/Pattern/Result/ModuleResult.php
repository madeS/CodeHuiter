<?php

namespace CodeHuiter\Pattern\Result;

class ModuleResult
{
    private const SUCCESS = 1;
    private const INCORRECT_FIELD = 2;
    private const ERROR = 3;
    private const SPECIFIC = 4;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string[]
     */
    protected $fields;

    /**
     * Result constructor.
     * @param int $type
     * @param string $message
     * @param string[] $fields
     */
    private function __construct(int $type, string $message, array $fields)
    {
        $this->type = $type;
        $this->message = $message;
        $this->fields = $fields;
    }

    /**
     * @param string $message
     * @return ModuleResult
     */
    public static function createSuccess(string $message = ''): ModuleResult
    {
        return new ModuleResult(self::SUCCESS, $message, []);
    }

    /**
     * @param string $message
     * @param string $field
     * @return ModuleResult
     */
    public static function createIncorrectField(string $message, string $field): ModuleResult
    {
        return new ModuleResult(self::INCORRECT_FIELD, $message, [$field]);
    }

    /**
     * @param string $message
     * @param string[] $fields
     * @return ModuleResult
     */
    public static function createIncorrectFields(string $message, array $fields): ModuleResult
    {
        return new ModuleResult(self::INCORRECT_FIELD, $message, $fields);
    }

    /**
     * @param string $message
     * @return ModuleResult
     */
    public static function createError(string $message): ModuleResult
    {
        return new ModuleResult(self::ERROR, $message, []);
    }

    /**
     * @param string $message
     * @param array $fields
     * @return ModuleResult
     */
    public static function createSpecific(string $message, array $fields = []): ModuleResult
    {
        return new ModuleResult(self::SPECIFIC, $message, $fields);
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->type === self::SUCCESS;
    }

    public function isIncorrectField(): bool
    {
        return $this->type === self::INCORRECT_FIELD;
    }

    public function isError(): bool
    {
        return $this->type === self::ERROR;
    }

    public function isSpecific(): bool
    {
        return $this->type === self::SPECIFIC;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }
}

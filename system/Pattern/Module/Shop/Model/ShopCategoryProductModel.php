<?php

namespace CodeHuiter\Pattern\Module\Shop\Model;

use CodeHuiter\Database\RelationalModel;

class ShopCategoryProductModel extends RelationalModel
{
    /** @var string */
    protected static $databaseServiceKey = 'db';
    /** @var string */
    protected static $table = 'TestWithTwoAutoIncrement';
    /** @var string[] */
    protected static $primaryFields = ['onePrimaryField', 'secondPrimaryField'];
    /** @var string */
    protected static $autoIncrementField = 'secondPrimaryField';

    /**
     * @var int
     */
    protected $onePrimaryField;

    /**
     * @var int
     */
    protected $secondPrimaryField;

    /**
     * @var string
     */
    protected $created_at;

    /**
     * @return int
     */
    public function getOnePrimaryField(): int
    {
        return $this->onePrimaryField;
    }

    /**
     * @param int $onePrimaryField
     */
    public function setOnePrimaryField(int $onePrimaryField): void
    {
        $this->onePrimaryField = $onePrimaryField;
    }

    /**
     * @return int
     */
    public function getSecondPrimaryField(): int
    {
        return $this->secondPrimaryField;
    }

    /**
     * @param int $secondPrimaryField
     */
    public function setSecondPrimaryField(int $secondPrimaryField): void
    {
        $this->secondPrimaryField = $secondPrimaryField;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    /**
     * @param string $created_at
     */
    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return "[{$this->onePrimaryField},{$this->secondPrimaryField},{$this->created_at}]";
    }


}
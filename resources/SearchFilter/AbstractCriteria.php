<?php

namespace Resources\SearchFilter;

use Resources\SearchCriteriaInterface;

abstract class AbstractCriteria implements SearchCriteriaInterface
{

    /**
     * 
     * @var string
     */
    protected $column;
    protected $value;

    /**
     * 
     * @var string|null
     */
    protected $table;

    public function __construct(string $column, $value, ?string $table = null)
    {
        $this->column = $column;
        $this->value = $value;
        $this->table = $table;
    }

    public function getTableColumnName(): string
    {
        return $this->table ? "{$this->table}.{$this->column}" : $this->column;
    }

}

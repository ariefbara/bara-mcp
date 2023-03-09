<?php

namespace Resources\OffsetLimit;

use ReflectionClass;
use Resources\Exception\RegularException;

class Order
{

    const ASC = 'ASC';
    const DESC = 'DESC';

    /**
     * 
     * @var string
     */
    protected $column;

    /**
     * 
     * @var string
     */
    protected $direction;

    /**
     * 
     * @var ?string
     */
    protected $table = null;

    protected function setDirection(string $direction = 'ASC')
    {
        $c = new ReflectionClass($this);
        if (!in_array($direction, $c->getConstants())) {
            throw RegularException::badRequest("bad request: invalid order direction");
        }
        $this->direction = $direction;
    }

    public function __construct(string $column, string $direction = 'ASC', ?string $table = null)
    {
        $this->column = $column;
        $this->direction = $direction;
        $this->table = $table;
    }

    //
    public function getTableColumnName(): string
    {
        return $this->table ? "{$this->table}.{$this->column}" : $this->column;
    }

    //
    public function getOrderStatement(): string
    {
        return "{$this->getTableColumnName()} {$this->direction}";
    }

}

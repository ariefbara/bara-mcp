<?php

namespace Query\Domain\Model\Shared;

class CsvEntry
{
    /**
     * 
     * @var array
     */
    protected $columns;
    
    public function getColumns()
    {
        return $this->columns;
    }

    public function __construct()
    {
        $this->columns = [];
    }
    
    public function addColumn($value): self
    {
        $this->columns[] = '"' . $value . '"';
        return $this;
    }
}

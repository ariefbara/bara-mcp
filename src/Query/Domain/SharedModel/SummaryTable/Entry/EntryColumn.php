<?php

namespace Query\Domain\SharedModel\SummaryTable\Entry;

class EntryColumn
{

    /**
     * 
     * @var int
     */
    protected $colNumber;
    protected $value;

    public function getColNumber(): int
    {
        return $this->colNumber;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __construct(int $colNumber, $value)
    {
        $this->colNumber = $colNumber;
        $this->value = $value;
    }

    public function toArray(): array
    {
        return [
            'colNumber' => $this->colNumber,
            'value' => $this->value,
        ];
    }

}

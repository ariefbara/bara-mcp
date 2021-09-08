<?php

namespace Query\Domain\SharedModel\SummaryTable;

use Query\Domain\SharedModel\SummaryTable\Entry\EntryColumn;

class Entry
{

    /**
     * 
     * @var array
     */
    protected $entryColumns;

    public function __construct(array $initialEntryColumns)
    {
        $this->entryColumns = [];
        foreach ($initialEntryColumns as $entryColumn) {
            $this->addEntryColumn($entryColumn);
        }
    }

    public function addEntryColumn(EntryColumn $entryColumn): void
    {
        $this->entryColumns[] = $entryColumn;
    }

    public function toRelationalArray(): array
    {
        $result = [];
        foreach ($this->entryColumns as $entryColumn) {
            $result[$entryColumn->getColNumber()] = $entryColumn->toArray();
        }
        return $result;
    }

    public function toSimplifiedArray(): array
    {
        $result = [];
        foreach ($this->entryColumns as $entryColumn) {
            $result[$entryColumn->getColNumber()] = $entryColumn->getValue();
        }
        return $result;
    }

}

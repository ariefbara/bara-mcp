<?php

namespace Query\Domain\SharedModel;

use Query\Domain\SharedModel\SummaryTable\Entry;

class SummaryTable
{
    /**
     * 
     * @var Entry[]
     */
    protected $entries;
    
    public function __construct(array $initialEntries)
    {
        $this->entries = [];
        foreach ($initialEntries as $entry) {
            $this->addEntry($entry);
        }
    }
    
    public function addEntry(Entry $entry): void
    {
        $this->entries[] = $entry;
    }
    
    public function toArraySummaryFormat(): array
    {
        foreach ($this->entries as $entry) {
            $result[] = $entry->toRelationalArray();
        }
        return $result;
    }
    
    /**
     * 
     * @param IHeaderColumn[] $headerColumns
     * @return array
     */
    public function toArraySummarySimplifiedFormat(array $headerColumns): array
    {
        $headerRow = [];
        foreach ($headerColumns as $headerColumn) {
            $headerRow[$headerColumn->getColNumber()] = $headerColumn->getLabel();
        }
        
        $result = [$headerRow];
        foreach ($this->entries as $entry) {
            $result[] = $entry->toSimplifiedArray();
        }
        return $result;
    }
    
    /**
     * 
     * @param IHeaderColumn[] $headerColumns
     * @return array
     */
    public function toArrayTranscriptSimplifiedFormat(array $headerColumns): array
    {
        $result = [];
        foreach ($headerColumns as $headerColumn) {
            $result[$headerColumn->getColNumber()][0] = $headerColumn->getLabel();
        }
        foreach ($this->entries as $rowNumber => $entry) {
            foreach ($entry->toSimplifiedArray() as $colNumber => $value) {
                $result[$colNumber][$rowNumber+1] = $value;
            }
        }
        return $result;
    }

}

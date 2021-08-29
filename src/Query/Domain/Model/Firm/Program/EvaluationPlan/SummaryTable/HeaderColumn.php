<?php

namespace Query\Domain\Model\Firm\Program\EvaluationPlan\SummaryTable;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\SharedModel\IHeaderColumn;
use Query\Domain\SharedModel\SummaryTable\Entry;
use Query\Domain\SharedModel\SummaryTable\Entry\EntryColumn;

class HeaderColumn implements IHeaderColumn
{

    /**
     * 
     * @var int
     */
    protected $colNumber;

    /**
     * 
     * @var IField
     */
    protected $field;

    public function getColNumber(): int
    {
        return $this->colNumber;
    }

    public function __construct(int $colNumber, IField $field)
    {
        $this->colNumber = $colNumber;
        $this->field = $field;
    }

    public function getLabel(): string
    {
        return $this->field->getLabel();
    }

    public function appendEntryColumnFromRecordToEntry(EvaluationReport $evaluationReport, Entry $entry): void
    {
        $entryColumn = new EntryColumn(
                $this->colNumber, $this->field->getCorrespondingValueFromRecord($evaluationReport));
        $entry->addEntryColumn($entryColumn);
    }

    public function toArray(): array
    {
        return [
            'colNumber' => $this->colNumber,
            'label' => $this->field->getLabel(),
        ];
    }

}

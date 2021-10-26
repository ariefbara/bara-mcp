<?php

namespace Query\Domain\Model\Firm\Program\EvaluationPlan;

use Query\Domain\Model\Firm\Program\EvaluationPlan;
use Query\Domain\Model\Firm\Program\EvaluationPlan\SummaryTable\HeaderColumn;
use Query\Domain\Model\Firm\Program\EvaluationPlan\SummaryTable\StaticHeaderColumn;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\SharedModel\SummaryTable;
use Query\Domain\SharedModel\SummaryTable\Entry;

class ClientTranscriptTable implements IContainSummaryTable
{
    /**
     * 
     * @var EvaluationPlan
     */
    protected $evaluationPlan;

    /**
     * 
     * @var HeaderColumn[]
     */
    protected $headerColumns;

    /**
     * 
     * @var SummaryTable
     */
    protected $summaryTable;
    
    protected function generateEntryFromEvaluationReport(EvaluationReport $evaluationReport): Entry
    {
        $mentorEntryColumn = new Entry\EntryColumn(1, $evaluationReport->getMentorPlusTeamName());
        $entry = new Entry([$mentorEntryColumn]);
        
        foreach ($this->headerColumns as $headerColumn) {
            $headerColumn->appendEntryColumnFromRecordToEntry($evaluationReport, $entry);
        }
        return $entry;
    }

    public function __construct(EvaluationReport $evaluationReport)
    {
        $this->headerColumns = [];
        $this->evaluationPlan = $evaluationReport->getEvaluationPlan();
        $this->evaluationPlan->appendAllFieldsAsHeaderColumnOfSummaryTable($this, 2);
        $this->summaryTable = new SummaryTable([$this->generateEntryFromEvaluationReport($evaluationReport)]);
    }
    
    public function addHeaderColumn(HeaderColumn $headerColumn): void
    {
        $this->headerColumns[] = $headerColumn;
    }
    
    public function canInclude(EvaluationReport $evaluationReport): bool
    {
        return $evaluationReport->evaluationPlanEquals($this->evaluationPlan);
    }
    
    public function includeEvaluationReport(EvaluationReport $evaluationReport): void
    {
        $this->summaryTable->addEntry($this->generateEntryFromEvaluationReport($evaluationReport));
    }
    
    public function getEvaluationPlanName(): string
    {
        return $this->evaluationPlan->getName();
    }
    
    public function toSimplifiedTranscriptFormatArray(bool $summaryStyleView = false): array
    {
        $mentorColumnHeader = new StaticHeaderColumn(1, 'mentor');
        $headerColumns = array_merge([$mentorColumnHeader], $this->headerColumns);
        if ($summaryStyleView) {
            return $this->summaryTable->toArraySummarySimplifiedFormat($headerColumns);
        } else {
            return $this->summaryTable->toArrayTranscriptSimplifiedFormat($headerColumns);
        }
    }
    
    public function toRelationalArray(): array
    {
        $result = [
            'id' => $this->evaluationPlan->getId(),
            'name' => $this->evaluationPlan->getName(),
        ];
        $tableHeaderRow = [
            1 => [
                'colNumber' => 1,
                'label' => 'mentor',
            ],
        ];
        foreach ($this->headerColumns as $headerColumn) {
            $tableHeaderRow[$headerColumn->getColNumber()] = $headerColumn->toArray();
        }
        $result['summaryTable']['header'] = $tableHeaderRow;
        $result['summaryTable']['entries'] = $this->summaryTable->toArraySummaryFormat();
        return $result;
    }


}

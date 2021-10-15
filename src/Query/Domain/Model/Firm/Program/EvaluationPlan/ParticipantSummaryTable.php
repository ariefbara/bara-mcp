<?php

namespace Query\Domain\Model\Firm\Program\EvaluationPlan;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Query\Domain\Model\Firm\Program\EvaluationPlan;
use Query\Domain\Model\Firm\Program\EvaluationPlan\SummaryTable\HeaderColumn;
use Query\Domain\Model\Firm\Program\EvaluationPlan\SummaryTable\StaticHeaderColumn;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\SharedModel\SummaryTable;
use Query\Domain\SharedModel\SummaryTable\Entry;
use Query\Domain\SharedModel\SummaryTable\Entry\EntryColumn;

class ParticipantSummaryTable implements IContainSummaryTable
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
        $participantEntryColumn = new EntryColumn(1, $evaluationReport->getParticipantName());
        $mentorEntryColumn = new EntryColumn(2, $evaluationReport->getMentorName());
        $entry = new Entry([$participantEntryColumn, $mentorEntryColumn]);
        
        foreach ($this->headerColumns as $headerColumn) {
            $headerColumn->appendEntryColumnFromRecordToEntry($evaluationReport, $entry);
        }
        
        return $entry;
    }
    
    public function __construct(EvaluationReport $evaluationReport)
    {
        $this->headerColumns = [];
        $this->evaluationPlan = $evaluationReport->getEvaluationPlan();
        $this->evaluationPlan->appendAllFieldsAsHeaderColumnOfSummaryTable($this, 3);
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
    
    public function saveToSpreadsheet(Spreadsheet $spreadsheet): void
    {
        $worksheet = $spreadsheet->createSheet();
        $worksheet->setTitle(substr($this->evaluationPlan->getName(), 0, 30));
        
        $participantColumn = new StaticHeaderColumn(1, 'participant');
        $mentorColumn = new StaticHeaderColumn(2, 'mentor');
        $headerColumns = array_merge([$participantColumn, $mentorColumn], $this->headerColumns);
        
        $worksheet->fromArray($this->summaryTable->toArraySummarySimplifiedFormat($headerColumns));
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
                'label' => 'participant',
            ],
            2 => [
                'colNumber' => 2,
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

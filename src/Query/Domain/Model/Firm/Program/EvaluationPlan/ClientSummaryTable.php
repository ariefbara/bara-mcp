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

class ClientSummaryTable implements IContainSummaryTable
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
    
    protected function getCompleteHeaderColumns(): array
    {
        $clientHeaderColumn = new StaticHeaderColumn(1, 'client');
        $mentorHeaderColumn = new StaticHeaderColumn(2, 'mentor');
        return array_merge([$clientHeaderColumn, $mentorHeaderColumn], $this->headerColumns);
    }
    
    protected function generateEntriesFromEvaluationReport(EvaluationReport $evaluationReport): array
    {
        $entries = [];
        $mentorEntry = new EntryColumn(2, $evaluationReport->getMentorName());
        foreach ($evaluationReport->getListOfClientPlusTeamName() as $clientAndTeamName) {
            $clientEntry = new EntryColumn(1, $clientAndTeamName);
            $entry = new Entry([$clientEntry, $mentorEntry]);
            foreach ($this->headerColumns as $headerColumn) {
                $headerColumn->appendEntryColumnFromRecordToEntry($evaluationReport, $entry);
            }
            $entries[] = $entry;
        }
        return $entries;
    }
    
    public function __construct(EvaluationReport $evaluationReport)
    {
        $this->headerColumns = [];
        $this->evaluationPlan = $evaluationReport->getEvaluationPlan();
        $this->evaluationPlan->appendAllFieldsAsHeaderColumnOfSummaryTable($this, $startColNumber = 3);
        $this->summaryTable = new SummaryTable($this->generateEntriesFromEvaluationReport($evaluationReport));
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
        foreach ($this->generateEntriesFromEvaluationReport($evaluationReport) as $entry) {
            $this->summaryTable->addEntry($entry);
        }
    }
    
    public function saveToSpreadsheet(Spreadsheet $spreadsheet): void
    {
        $worksheet = $spreadsheet->createSheet();
        $worksheet->setTitle(substr($this->evaluationPlan->getName(), 0, 30));
        $worksheet->fromArray($this->summaryTable->toArraySummarySimplifiedFormat($this->getCompleteHeaderColumns()));
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
                'label' => 'client',
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

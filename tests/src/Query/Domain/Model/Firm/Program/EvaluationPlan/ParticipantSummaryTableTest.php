<?php

namespace Query\Domain\Model\Firm\Program\EvaluationPlan;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Query\Domain\Model\Firm\Program\EvaluationPlan;
use Query\Domain\Model\Firm\Program\EvaluationPlan\SummaryTable\HeaderColumn;
use Query\Domain\Model\Firm\Program\EvaluationPlan\SummaryTable\StaticHeaderColumn;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\SharedModel\SummaryTable;
use Query\Domain\SharedModel\SummaryTable\Entry;
use Query\Domain\SharedModel\SummaryTable\Entry\EntryColumn;
use Tests\TestBase;

class ParticipantSummaryTableTest extends TestBase
{
    protected $participantSummaryTable;
    protected $evaluationPlan, $evaluationPlanId = 'ev-plan-id', $evaluationPlanName = 'evaluation plan name';
    protected $headerColumnOne, $colOneNumber = 3, $colOneLabel = 'field one label', $colOneRelationalArray = ['colNumber' => 3, 'label' => 'field one label'];
    protected $headerColumnTwo, $colTwoNumber = 4, $colTwoLabel = 'field two label', $colTwoRelationalArray = ['colNumber' => 3, 'label' => 'field two label'];
    protected $summaryTable;
    protected $evaluationReport, $participantName = 'participant name', $mentorName = 'mentor name';
    
    protected $spreadsheet, $worksheet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participantSummaryTable = new TestableParticipantSummaryTable($this->buildMockOfClass(EvaluationReport::class));
        $this->evaluationPlan = $this->buildMockOfClass(EvaluationPlan::class);
        $this->evaluationPlan->expects($this->any())->method('getId')->willReturn($this->evaluationPlanId);
        $this->evaluationPlan->expects($this->any())->method('getName')->willReturn($this->evaluationPlanName);
        $this->participantSummaryTable->evaluationPlan = $this->evaluationPlan;
        
        $this->headerColumnOne = $this->buildMockOfClass(HeaderColumn::class);
        $this->headerColumnOne->expects($this->any())->method('getColNumber')->willReturn($this->colOneNumber);
        $this->headerColumnOne->expects($this->any())->method('getLabel')->willReturn($this->colOneLabel);
        $this->headerColumnOne->expects($this->any())->method('toArray')->willReturn($this->colOneRelationalArray);
        
        $this->headerColumnTwo = $this->buildMockOfClass(HeaderColumn::class);
        $this->headerColumnTwo->expects($this->any())->method('getColNumber')->willReturn($this->colTwoNumber);
        $this->headerColumnTwo->expects($this->any())->method('getLabel')->willReturn($this->colTwoLabel);
        $this->headerColumnTwo->expects($this->any())->method('toArray')->willReturn($this->colTwoRelationalArray);
        
        $this->participantSummaryTable->headerColumns[] = $this->headerColumnOne;
        $this->participantSummaryTable->headerColumns[] = $this->headerColumnTwo;
        
        $this->summaryTable = $this->buildMockOfClass(SummaryTable::class);
        $this->participantSummaryTable->summaryTable = $this->summaryTable;
        
        $this->evaluationReport = $this->buildMockOfClass(EvaluationReport::class);
        $this->evaluationReport->expects($this->any())
                ->method('getParticipantName')
                ->willReturn($this->participantName);
        $this->evaluationReport->expects($this->any())
                ->method('getMentorName')
                ->willReturn($this->mentorName);
        
        $this->spreadsheet = $this->buildMockOfClass(Spreadsheet::class);
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
    }
 
    protected function construct()
    {
        $this->evaluationReport->expects($this->once())
                ->method('getEvaluationPlan')
                ->willReturn($this->evaluationPlan);
        return new TestableParticipantSummaryTable($this->evaluationReport);
    }
    public function test_construct_setHeaderColumnsEvaluationPlan()
    {
        $participantSummaryTable = $this->construct();
        $this->assertEquals([], $participantSummaryTable->headerColumns);
        $this->assertEquals($this->evaluationPlan, $participantSummaryTable->evaluationPlan);
    }
    public function test_construct_appendOfEvaluationPlanFieldsAsHeaderColumns()
    {
        $this->evaluationPlan->expects($this->once())
                ->method('appendAllFieldsAsHeaderColumnOfSummaryTable')
                ->with($this->anything(), 3);
        $this->construct();
    }
    public function test_construct_setSummaryTable()
    {
        $participantEntryColumn = new EntryColumn(1, $this->participantName);
        $mentorEntryColumn = new EntryColumn(2, $this->mentorName);
        $entry = new Entry([$participantEntryColumn, $mentorEntryColumn]);
        
        $summaryTable = new SummaryTable([$entry]);
        $participantSummaryTable = $this->construct();
        $this->assertEquals($summaryTable, $participantSummaryTable->summaryTable);
    }
    
    public function test_addHeaderColumn_appendHeaderColumn()
    {
        $this->participantSummaryTable->headerColumns = [];
        $this->participantSummaryTable->addHeaderColumn($this->headerColumnOne);
        $this->assertEquals([$this->headerColumnOne], $this->participantSummaryTable->headerColumns);
    }
    
    public function test_canInclude_returnEvaluationReportsEvaluationPlanEqualsResult()
    {
        $this->evaluationReport->expects($this->once())
                ->method('evaluationPlanEquals')
                ->with($this->evaluationPlan);
        $this->participantSummaryTable->canInclude($this->evaluationReport);
    }
    
    protected function includeEvaluationReport()
    {
        $this->participantSummaryTable->includeEvaluationReport($this->evaluationReport);
    }
    public function test_includeEvaluationReport_addEntryToSummaryTable()
    {
        $participantEntryColumn = new EntryColumn(1, $this->participantName);
        $mentorEntryColumn = new EntryColumn(2, $this->mentorName);
        $entry = new Entry([$participantEntryColumn, $mentorEntryColumn]);
        
        $this->summaryTable->expects($this->once())
                ->method('addEntry')
                ->with($entry);
        $this->includeEvaluationReport();
    }
    public function test_includeEvaluationReport_headersappendAllEntryColumnToEntry()
    {
        $participantEntryColumn = new EntryColumn(1, $this->participantName);
        $mentorEntryColumn = new EntryColumn(2, $this->mentorName);
        $entry = new Entry([$participantEntryColumn, $mentorEntryColumn]);
        
        $this->headerColumnOne->expects($this->once())
                ->method('appendEntryColumnFromRecordToEntry')
                ->with($this->evaluationReport, $entry);
        
        $this->headerColumnTwo->expects($this->once())
                ->method('appendEntryColumnFromRecordToEntry')
                ->with($this->evaluationReport, $entry);
        
        $this->includeEvaluationReport();
    }
    
    protected function saveToSpreadsheet()
    {
        $this->spreadsheet->expects($this->any())
                ->method('createSheet')
                ->willReturn($this->worksheet);
        $this->participantSummaryTable->saveToSpreadsheet($this->spreadsheet);
    }
    public function test_saveToSpreadsheet_createSheetAndSetTitle()
    {
        $this->worksheet->expects($this->once())
                ->method('setTitle')
                ->with($this->evaluationPlanName);
        $this->saveToSpreadsheet();
    }
    public function test_saveToSpreadsheet_saveSimplifiedTableArrayFromSummaryTableTableToSpreadsheet()
    {
        $headerColumns = [
            new StaticHeaderColumn(1, 'participant'),
            new StaticHeaderColumn(2, 'mentor'),
            $this->headerColumnOne,
            $this->headerColumnTwo,
        ];
        $this->summaryTable->expects($this->once())
                ->method('toArraySummarySimplifiedFormat')
                ->with($headerColumns)
                ->willReturn($simplifiedSummaryTableArray = ['string represent simplified array']);
        
        $this->worksheet->expects($this->once())
                ->method('fromArray')
                ->with($simplifiedSummaryTableArray);
        $this->saveToSpreadsheet();
    }
    
    public function test_toRelationalArray_returnRelationalArray()
    {
        $this->summaryTable->expects($this->once())
                ->method('toArraySummaryFormat')
                ->willReturn($SummaryTableArray = [
                    ['string represent first entry'],
                    ['string represent second entry'],
                ]);
        $participantHeader = [
            'colNumber' => 1,
            'label' => 'participant',
        ];
        $mentorHeader = [
            'colNumber' => 2,
            'label' => 'mentor',
        ];
        $table = [
            'id' => $this->evaluationPlanId,
            'name' => $this->evaluationPlanName,
            'summaryTable' => [
                'header' => [1 => $participantHeader, 2 => $mentorHeader, 3 => $this->colOneRelationalArray, 4 => $this->colTwoRelationalArray],
                'entries' => $SummaryTableArray,
            ],
        ];
        $this->assertEquals($table, $this->participantSummaryTable->toRelationalArray());
    }
}

class TestableParticipantSummaryTable extends ParticipantSummaryTable
{
    public $evaluationPlan;
    public $headerColumns;
    public $summaryTable;
}

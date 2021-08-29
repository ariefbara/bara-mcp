<?php

namespace Query\Domain\Model\Firm\Program\EvaluationPlan;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Query\Domain\Model\Firm\Program\EvaluationPlan;
use Query\Domain\Model\Firm\Program\EvaluationPlan\SummaryTable\HeaderColumn;
use Query\Domain\Model\Firm\Program\EvaluationPlan\SummaryTable\StaticHeaderColumn;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\SharedModel\SummaryTable;
use Tests\TestBase;

class ClientSummaryTableTest extends TestBase
{
    protected $clientSummaryTable;
    protected $evaluationPlan, $evaluationPlanId = 'ev-plan-id', $evaluationPlanName = 'evaluation plan name';
    protected $headerColumnOne, $colOneNumber = 3, $colOneLabel = 'field one label', $colOneRelationalArray = ['colNumber' => 3, 'label' => 'field one label'];
    protected $headerColumnTwo, $colTwoNumber = 4, $colTwoLabel = 'field two label', $colTwoRelationalArray = ['colNumber' => 3, 'label' => 'field two label'];
    protected $summaryTable;
    protected $evaluationReport, $listOfClientPlusTeamName = ['client one (of team one)', 'client two (of team two)'], 
            $mentorName = 'mentor name';
    
    protected $spreadsheet, $worksheet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientSummaryTable = new TestableClientSummaryTable($this->buildMockOfClass(EvaluationReport::class));
        $this->evaluationPlan = $this->buildMockOfClass(EvaluationPlan::class);
        $this->evaluationPlan->expects($this->any())->method('getId')->willReturn($this->evaluationPlanId);
        $this->evaluationPlan->expects($this->any())->method('getName')->willReturn($this->evaluationPlanName);
        $this->clientSummaryTable->evaluationPlan = $this->evaluationPlan;
        
        $this->headerColumnOne = $this->buildMockOfClass(HeaderColumn::class);
        $this->headerColumnOne->expects($this->any())->method('getColNumber')->willReturn($this->colOneNumber);
        $this->headerColumnOne->expects($this->any())->method('getLabel')->willReturn($this->colOneLabel);
        $this->headerColumnOne->expects($this->any())->method('toArray')->willReturn($this->colOneRelationalArray);
        
        $this->headerColumnTwo = $this->buildMockOfClass(HeaderColumn::class);
        $this->headerColumnTwo->expects($this->any())->method('getColNumber')->willReturn($this->colTwoNumber);
        $this->headerColumnTwo->expects($this->any())->method('getLabel')->willReturn($this->colTwoLabel);
        $this->headerColumnTwo->expects($this->any())->method('toArray')->willReturn($this->colTwoRelationalArray);
        
        $this->clientSummaryTable->headerColumns[] = $this->headerColumnOne;
        $this->clientSummaryTable->headerColumns[] = $this->headerColumnTwo;
        
        $this->summaryTable = $this->buildMockOfClass(SummaryTable::class);
        $this->clientSummaryTable->summaryTable = $this->summaryTable;
        
        $this->evaluationReport = $this->buildMockOfClass(EvaluationReport::class);
        $this->evaluationReport->expects($this->any())
                ->method('getListOfClientPlusTeamName')
                ->willReturn($this->listOfClientPlusTeamName);
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
        return new TestableClientSummaryTable($this->evaluationReport);
    }
    public function test_construct_setEvaluationPlan()
    {
        $clientSummaryTable = $this->construct();
        $this->assertEquals($this->evaluationPlan, $clientSummaryTable->evaluationPlan);
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
        $initialEntryColumnsOne = [
            new SummaryTable\Entry\EntryColumn(1, $this->listOfClientPlusTeamName[0]),
            new SummaryTable\Entry\EntryColumn(2, $this->mentorName),
        ];
        $initialEntryColumnsTwo = [
            new SummaryTable\Entry\EntryColumn(1, $this->listOfClientPlusTeamName[1]),
            new SummaryTable\Entry\EntryColumn(2, $this->mentorName),
        ];
        $entryOne = new SummaryTable\Entry($initialEntryColumnsOne);
        $entryTwo = new SummaryTable\Entry($initialEntryColumnsTwo);
        
        $summaryTable = new SummaryTable([$entryOne, $entryTwo]);
        $clientSummaryTable = $this->construct();
        $this->assertEquals($summaryTable, $clientSummaryTable->summaryTable);
    }
    
    public function test_addHeaderColumn_appendHeaderColumn()
    {
        $this->clientSummaryTable->headerColumns = [];
        $this->clientSummaryTable->addHeaderColumn($this->headerColumnOne);
        $this->assertEquals([$this->headerColumnOne], $this->clientSummaryTable->headerColumns);
    }
    
    public function test_canInclude_returnEvaluationReportsEvaluationPlanEqualsResult()
    {
        $this->evaluationReport->expects($this->once())
                ->method('evaluationPlanEquals')
                ->with($this->evaluationPlan);
        $this->clientSummaryTable->canInclude($this->evaluationReport);
    }
    
    protected function includeEvaluationReport()
    {
        $this->clientSummaryTable->includeEvaluationReport($this->evaluationReport);
    }
    public function test_includeEvaluationReport_addEntryToSummaryTable()
    {
        $initialEntryColumnsOne = [
            new SummaryTable\Entry\EntryColumn(1, $this->listOfClientPlusTeamName[0]),
            new SummaryTable\Entry\EntryColumn(2, $this->mentorName),
        ];
        $initialEntryColumnsTwo = [
            new SummaryTable\Entry\EntryColumn(1, $this->listOfClientPlusTeamName[1]),
            new SummaryTable\Entry\EntryColumn(2, $this->mentorName),
        ];
        $entryOne = new SummaryTable\Entry($initialEntryColumnsOne);
        $entryTwo = new SummaryTable\Entry($initialEntryColumnsTwo);
        
        $this->summaryTable->expects($this->exactly(2))
                ->method('addEntry')
                ->withConsecutive([$entryOne], [$entryTwo]);
        $this->includeEvaluationReport();
    }
    public function test_includeEvaluationReport_headersappendAllEntryColumnToEntry()
    {
        $initialEntryColumnsOne = [
            new SummaryTable\Entry\EntryColumn(1, $this->listOfClientPlusTeamName[0]),
            new SummaryTable\Entry\EntryColumn(2, $this->mentorName),
        ];
        $initialEntryColumnsTwo = [
            new SummaryTable\Entry\EntryColumn(1, $this->listOfClientPlusTeamName[1]),
            new SummaryTable\Entry\EntryColumn(2, $this->mentorName),
        ];
        $entryOne = new SummaryTable\Entry($initialEntryColumnsOne);
        $entryTwo = new SummaryTable\Entry($initialEntryColumnsTwo);
        
        $this->headerColumnOne->expects($this->exactly(2))
                ->method('appendEntryColumnFromRecordToEntry')
                ->withConsecutive([$this->evaluationReport, $entryOne], [$this->evaluationReport, $entryTwo]);
        
        $this->headerColumnTwo->expects($this->exactly(2))
                ->method('appendEntryColumnFromRecordToEntry')
                ->withConsecutive([$this->evaluationReport, $entryOne], [$this->evaluationReport, $entryTwo]);
        $this->includeEvaluationReport();
    }
    
    protected function saveToSpreadsheet()
    {
        $this->spreadsheet->expects($this->any())
                ->method('createSheet')
                ->willReturn($this->worksheet);
        $this->clientSummaryTable->saveToSpreadsheet($this->spreadsheet);
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
            new StaticHeaderColumn(1, 'client'),
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
        $clientHeader = [
            'colNumber' => 1,
            'label' => 'client',
        ];
        $mentorHeader = [
            'colNumber' => 2,
            'label' => 'mentor',
        ];
        $table = [
            'id' => $this->evaluationPlanId,
            'name' => $this->evaluationPlanName,
            'summaryTable' => [
                'header' => [1 => $clientHeader, 2 => $mentorHeader, 3 => $this->colOneRelationalArray, 4 => $this->colTwoRelationalArray],
                'entries' => $SummaryTableArray,
            ],
        ];
        $this->assertEquals($table, $this->clientSummaryTable->toRelationalArray());
    }
}

class TestableClientSummaryTable extends ClientSummaryTable
{
    public $evaluationPlan;
    public $headerColumns;
    public $summaryTable;
}

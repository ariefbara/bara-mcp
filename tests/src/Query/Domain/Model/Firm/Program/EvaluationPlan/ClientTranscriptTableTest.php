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

class ClientTranscriptTableTest extends TestBase
{
    protected $clientTranscripTable;
    protected $evaluationPlan, $evaluationPlanId = 'ev-plan-id', $evaluationPlanName = 'evaluation plan name';
    protected $headerColumnOne, $colOneNumber = 2, $colOneLabel = 'field one label', $colOneRelationalArray = ['colNumber' => 3, 'label' => 'field one label'];
    protected $headerColumnTwo, $colTwoNumber = 3, $colTwoLabel = 'field two label', $colTwoRelationalArray = ['colNumber' => 3, 'label' => 'field two label'];
    protected $summaryTable;
    
    protected $evaluationReport, $mentorName = 'mentor name';
    protected $spreadsheet, $worksheet;
    protected $summaryStyleView = false;


    protected function setUp(): void
    {
        parent::setUp();
        
        $this->evaluationReport = $this->buildMockOfClass(EvaluationReport::class);
        $this->clientTranscripTable = new TestableClientTranscriptTable($this->evaluationReport);
        
        $this->evaluationPlan = $this->buildMockOfClass(EvaluationPlan::class);
        $this->evaluationPlan->expects($this->any())->method('getId')->willReturn($this->evaluationPlanId);
        $this->evaluationPlan->expects($this->any())->method('getName')->willReturn($this->evaluationPlanName);
        $this->clientTranscripTable->evaluationPlan = $this->evaluationPlan;
        
        $this->headerColumnOne = $this->buildMockOfClass(HeaderColumn::class);
        $this->headerColumnOne->expects($this->any())->method('getColNumber')->willReturn($this->colOneNumber);
        $this->headerColumnOne->expects($this->any())->method('getLabel')->willReturn($this->colOneLabel);
        $this->headerColumnOne->expects($this->any())->method('toArray')->willReturn($this->colOneRelationalArray);
        
        $this->headerColumnTwo = $this->buildMockOfClass(HeaderColumn::class);
        $this->headerColumnTwo->expects($this->any())->method('getColNumber')->willReturn($this->colTwoNumber);
        $this->headerColumnTwo->expects($this->any())->method('getLabel')->willReturn($this->colTwoLabel);
        $this->headerColumnTwo->expects($this->any())->method('toArray')->willReturn($this->colTwoRelationalArray);
        
        $this->clientTranscripTable->headerColumns[] = $this->headerColumnOne;
        $this->clientTranscripTable->headerColumns[] = $this->headerColumnTwo;
        
        $this->summaryTable = $this->buildMockOfClass(SummaryTable::class);
        $this->clientTranscripTable->summaryTable = $this->summaryTable;
        
        $this->evaluationReport->expects($this->any())
                ->method('getMentorPlusTeamName')
                ->willReturn($this->mentorName);
        
        $this->spreadsheet = $this->buildMockOfClass(Spreadsheet::class);
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
    }
    
    protected function construct()
    {
        $this->evaluationReport->expects($this->once())
                ->method('getEvaluationPlan')
                ->willReturn($this->evaluationPlan);
        return new TestableClientTranscriptTable($this->evaluationReport);
    }
    public function test_construct_setProperties()
    {
        $clientTranscripTable = $this->construct();
        $this->assertEquals($this->evaluationPlan, $clientTranscripTable->evaluationPlan);
        $this->assertEquals([], $clientTranscripTable->headerColumns);
    }
    public function test_construct_appendOfEvaluationPlanFieldsAsHeaderColumns()
    {
        $this->evaluationPlan->expects($this->once())
                ->method('appendAllFieldsAsHeaderColumnOfSummaryTable')
                ->with($this->anything(), 2);
        $this->construct();
    }
    public function test_construct_setSummaryTable()
    {
        $entryMentorColumn = new EntryColumn(1, $this->mentorName);
        $initialEntry = new Entry([$entryMentorColumn]);
        
        $summaryTable = new SummaryTable([$initialEntry]);
        $clientTranscripTable = $this->construct();
        $this->assertEquals($summaryTable, $clientTranscripTable->summaryTable);
    }
    
    public function test_addHeaderColumn_appendHeaderColumn()
    {
        $this->clientTranscripTable->headerColumns = [];
        $this->clientTranscripTable->addHeaderColumn($this->headerColumnOne);
        $this->assertEquals([$this->headerColumnOne], $this->clientTranscripTable->headerColumns);
    }
    
    public function test_canInclude_returnEvaluationReportsEvaluationPlanEqualsResult()
    {
        $this->evaluationReport->expects($this->once())
                ->method('evaluationPlanEquals')
                ->with($this->evaluationPlan);
        $this->clientTranscripTable->canInclude($this->evaluationReport);
    }
    
    protected function includeEvaluationReport()
    {
        $this->clientTranscripTable->includeEvaluationReport($this->evaluationReport);
    }
    public function test_includeEvaluationReport_addEntryToSummaryTable()
    {
        $entryMentorColumn = new EntryColumn(1, $this->mentorName);
        $entry = new Entry([$entryMentorColumn]);
        
        $this->summaryTable->expects($this->once())
                ->method('addEntry')
                ->with($entry);
        $this->includeEvaluationReport();
    }
    public function test_includeEvaluationReport_headersappendAllEntryColumnToEntry()
    {
        $entryMentorColumn = new EntryColumn(1, $this->mentorName);
        $entry = new Entry([$entryMentorColumn]);
        
        $this->headerColumnOne->expects($this->once())
                ->method('appendEntryColumnFromRecordToEntry')
                ->with($this->evaluationReport, $entry);
        
        $this->headerColumnTwo->expects($this->once())
                ->method('appendEntryColumnFromRecordToEntry')
                ->with($this->evaluationReport, $entry);
        
        $this->includeEvaluationReport();
    }
    
    public function test_getEvaluationPlanName_returnEvaluationPlanName()
    {
        $this->evaluationPlan->expects($this->once())
                ->method('getName');
        $this->clientTranscripTable->getEvaluationPlanName();
    }
    
    protected function toSimplifiedTranscriptFormatArray()
    {
        return $this->clientTranscripTable->toSimplifiedTranscriptFormatArray($this->summaryStyleView);
    }
    public function test_toSimplifiedTranscriptFormatArray_returnSummaryTableSimplifiedTranscriptFormatResult()
    {
        $mentorHeaderColumn = new StaticHeaderColumn(1, 'mentor');
        $this->summaryTable->expects($this->once())
                ->method('toArrayTranscriptSimplifiedFormat')
                ->with([$mentorHeaderColumn, $this->headerColumnOne, $this->headerColumnTwo]);
        $this->toSimplifiedTranscriptFormatArray();
    }
    public function test_toSimplifiedTranscriptFormatArray_summaryStyleViewSet_returnSummaryTableSimplifiedTranscriptFormatResultInSummaryStyle()
    {
        $this->summaryStyleView = true;
        $mentorHeaderColumn = new StaticHeaderColumn(1, 'mentor');
        $this->summaryTable->expects($this->once())
                ->method('toArraySummarySimplifiedFormat')
                ->with([$mentorHeaderColumn, $this->headerColumnOne, $this->headerColumnTwo]);
        $this->toSimplifiedTranscriptFormatArray();
    }
    
    public function test_toRelationalArray_returnRelationalArray()
    {
        $this->summaryTable->expects($this->once())
                ->method('toArraySummaryFormat')
                ->willReturn($SummaryTableArray = [
                    ['string represent first entry'],
                    ['string represent second entry'],
                ]);
        $mentorHeader = [
            'colNumber' => 1,
            'label' => 'mentor',
        ];
        $table = [
            'id' => $this->evaluationPlanId,
            'name' => $this->evaluationPlanName,
            'summaryTable' => [
                'header' => [1 => $mentorHeader, 2 => $this->colOneRelationalArray, 3 => $this->colTwoRelationalArray],
                'entries' => $SummaryTableArray,
            ],
        ];
        $this->assertEquals($table, $this->clientTranscripTable->toRelationalArray());
    }
}

class TestableClientTranscriptTable extends ClientTranscriptTable
{
    public $evaluationPlan;
    public $headerColumns;
    public $summaryTable;
}

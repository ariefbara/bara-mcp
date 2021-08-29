<?php

namespace Query\Domain\Task\InProgram;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Query\Domain\Model\Firm\Program\EvaluationPlan\ParticipantSummaryTable;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Tests\TestBase;

class ParticipantEvaluationReportSummaryResultTest extends TestBase
{
    protected $participantEvaluationReportSummaryResult;
    protected $participantSummaryTableOne;
    protected $participantSummaryTableTwo;
    
    protected $evaluationReport;
    protected $spreadsheet;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participantEvaluationReportSummaryResult = new TestableParticipantEvaluationReportSummaryResult();
        
        $this->participantSummaryTableOne = $this->buildMockOfClass(ParticipantSummaryTable::class);
        $this->participantSummaryTableTwo = $this->buildMockOfClass(ParticipantSummaryTable::class);
        $this->participantEvaluationReportSummaryResult->participantSummaryTables[] = $this->participantSummaryTableOne;
        $this->participantEvaluationReportSummaryResult->participantSummaryTables[] = $this->participantSummaryTableTwo;
        
        $this->evaluationReport = $this->buildMockOfClass(EvaluationReport::class);
        $this->spreadsheet = $this->buildMockOfClass(Spreadsheet::class);
    }
    
    public function test_construct_setProperties()
    {
        $participantEvaluationReportSummaryResult = new TestableParticipantEvaluationReportSummaryResult();
        $this->assertEquals([], $participantEvaluationReportSummaryResult->participantSummaryTables);
    }
    
    protected function includeEvaluationReport()
    {
        $this->participantEvaluationReportSummaryResult->includeEvaluationReport($this->evaluationReport);
    }
    public function test_includeEvaluationReport_addNewParticipantSummaryTable()
    {
        $this->includeEvaluationReport();
        $this->assertEquals(3, count($this->participantEvaluationReportSummaryResult->participantSummaryTables));
        $this->assertInstanceOf(ParticipantSummaryTable::class, $this->participantEvaluationReportSummaryResult->participantSummaryTables[1]);
    }
    public function test_includeEvaluationReport_alreadyHasSummaryTableCorrepondToEvaluationReport_includeInExistingTable()
    {
        $this->participantSummaryTableOne->expects($this->once())
                ->method('canInclude')
                ->with($this->evaluationReport)
                ->willReturn(true);
        $this->participantSummaryTableOne->expects($this->once())
                ->method('includeEvaluationReport')
                ->with($this->evaluationReport);
        $this->includeEvaluationReport();
    }
    public function test_includeEvaluationReport_alreadyHasSummaryTableCorrepondToEvaluationReport_preventAddNewTable()
    {
        $this->participantSummaryTableTwo->expects($this->once())
                ->method('canInclude')
                ->with($this->evaluationReport)
                ->willReturn(true);
        $this->includeEvaluationReport();
        $this->assertEquals(2, count($this->participantEvaluationReportSummaryResult->participantSummaryTables));
    }
    
    protected function saveToSpreadsheet()
    {
        $this->participantEvaluationReportSummaryResult->saveToSpreadsheet($this->spreadsheet);
    }
    public function test_saveToSpreadsheet_saveEachParticipantSummaryTableToSpreadsheet()
    {
        $this->participantSummaryTableOne->expects($this->once())
                ->method('saveToSpreadsheet');
        $this->participantSummaryTableTwo->expects($this->once())
                ->method('saveToSpreadsheet');
        $this->saveToSpreadsheet();
    }
    
    protected function toRelationalArray()
    {
        return $this->participantEvaluationReportSummaryResult->toRelationalArray();
    }
    public function test_toRelationalArray_returnRelationalArrayFromAllParticipantSummaryTable()
    {
        $this->participantSummaryTableOne->expects($this->once())
                ->method('toRelationalArray')
                ->willReturn($tableOneArray = ['string represent array of table one']);
        $this->participantSummaryTableTwo->expects($this->once())
                ->method('toRelationalArray')
                ->willReturn($tableTwoArray = ['string represent array of table two']);
        $this->assertEquals([
            $tableOneArray,
            $tableTwoArray,
        ], $this->toRelationalArray());
    }
}

class TestableParticipantEvaluationReportSummaryResult extends ParticipantEvaluationReportSummaryResult
{
    public $participantSummaryTables;
}

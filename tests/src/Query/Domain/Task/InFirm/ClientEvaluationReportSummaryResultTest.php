<?php

namespace Query\Domain\Task\InFirm;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Query\Domain\Model\Firm\Program\EvaluationPlan\ClientSummaryTable;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Tests\TestBase;

class ClientEvaluationReportSummaryResultTest extends TestBase
{
    protected $result, $clientSummaryTable;
    protected $evaluationReport;
    protected $spreadsheet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->result = new TestableClientEvaluationReportSummaryResult();
        $this->clientSummaryTable = $this->buildMockOfClass(ClientSummaryTable::class);
        $this->result->clientSummaryTables[] = $this->clientSummaryTable;
        
        $this->evaluationReport = $this->buildMockOfClass(EvaluationReport::class);
        
        $this->spreadsheet = $this->buildMockOfClass(Spreadsheet::class);
    }
    
    protected function includeEvaluationReport()
    {
        $this->result->includeEvaluationReport($this->evaluationReport);
    }
    public function test_includeEvaluationReport_addClientSummaryTable()
    {
        $this->includeEvaluationReport();
        $this->assertEquals(2, count($this->result->clientSummaryTables));
        $this->assertInstanceOf(ClientSummaryTable::class, $this->result->clientSummaryTables[1]);
    }
    public function test_includeEvaluationReport_hasClientSummaryTableCorrespondWithEvaluationReportPlan_includeInCorrespondingExistingTable()
    {
        $this->clientSummaryTable->expects($this->once())
                ->method('canInclude')
                ->with($this->evaluationReport)
                ->willReturn(true);
        $this->clientSummaryTable->expects($this->once())
                ->method('includeEvaluationReport')
                ->with($this->evaluationReport);
        $this->includeEvaluationReport();
    }
    public function test_includeEvaluationReport_hasClientSummaryTableCorrespondWithEvaluationReportPlan_preventAddNewClientSummaryTable()
    {
        $this->clientSummaryTable->expects($this->once())
                ->method('canInclude')
                ->with($this->evaluationReport)
                ->willReturn(true);
        $this->includeEvaluationReport();
        $this->assertEquals(1, count($this->result->clientSummaryTables));
    }
    
    protected function saveToSpreadsheet()
    {
        $this->result->saveToSpreadsheet($this->spreadsheet);
    }
    public function test_saveToSpreadsheet_saveAllClientSummaryTableToSpreadsheet()
    {
        $this->clientSummaryTable->expects($this->once())
                ->method('saveToSpreadsheet')
                ->with($this->spreadsheet);
        $this->saveToSpreadsheet();
    }
    
    public function test_toRelationalArray_returnSetOfAllClientSummaryTableArray()
    {
        $this->clientSummaryTable->expects($this->once())
                ->method('toRelationalArray')
                ->willReturn($clientSummaryRelationalArray = [
                    'id' => "evaluation-plan-id",
                    'name' => "evaluation plan name",
                    'summaryTable' => [
                        'header' => ['client', 'mentor', 'field one'],
                        'entries' => [
                            ['client name', 'mentor name', 'field one value'],
                        ],
                    ],
                ]);
        $result = [
            $clientSummaryRelationalArray,
        ];
        $this->assertEquals($result, $this->result->toRelationalArray());
    }
}

class TestableClientEvaluationReportSummaryResult extends ClientEvaluationReportSummaryResult
{
    public $clientSummaryTables;
}

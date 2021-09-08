<?php

namespace Query\Domain\Model\Firm\Client;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\Client\TranscriptTable;
use Query\Domain\Model\Firm\Program\EvaluationPlan\ClientTranscriptTable;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Tests\TestBase;

class TranscriptTableTest extends TestBase
{
    protected $client, $clientId = 'client-id', $clientName = 'client name';
    protected $transcriptTable;
    protected $clientTranscriptTableOne, $evaluationPlanOneName = 'evaluation plan one name';
    protected $clientTranscriptTableTwo, $evaluationPlanTwoName = 'evaluation plan two name';
    
    protected $evaluationReport;
    protected $spreadsheet, $worksheet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->client->expects($this->any())->method('getFullName')->willReturn($this->clientName);
        $this->client->expects($this->any())->method('getId')->willReturn($this->clientId);
        
        $this->transcriptTable = new TestableTranscriptTable($this->client);
        
        $this->clientTranscriptTableOne = $this->buildMockOfClass(ClientTranscriptTable::class);
        $this->clientTranscriptTableOne->expects($this->any())->method('getEvaluationPlanName')->willReturn($this->evaluationPlanOneName);
        $this->clientTranscriptTableTwo = $this->buildMockOfClass(ClientTranscriptTable::class);
        $this->clientTranscriptTableTwo->expects($this->any())->method('getEvaluationPlanName')->willReturn($this->evaluationPlanTwoName);
        
        $this->transcriptTable->clientTranscriptTables[] = $this->clientTranscriptTableOne;
        $this->transcriptTable->clientTranscriptTables[] = $this->clientTranscriptTableTwo;
        
        $this->evaluationReport = $this->buildMockOfClass(EvaluationReport::class);
        
        $this->spreadsheet = $this->buildMockOfClass(Spreadsheet::class);
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
    }
    
    public function test_construct_setProperties()
    {
        $transcriptTable = new TestableTranscriptTable($this->client);
        $this->assertEquals($this->client, $transcriptTable->client);
    }
    
    public function test_canInclude_returnEvaluationReportCorrespondWithClientStatusResult()
    {
        $this->evaluationReport->expects($this->once())
                ->method('correspondWithClient')
                ->with($this->client);
        $this->transcriptTable->canInclude($this->evaluationReport);
    }
    
    protected function includeEvaluationReport()
    {
        $this->transcriptTable->includeEvaluationReport($this->evaluationReport);
    }
    public function test_includeEvaluationReport_addNewEvaluationReport()
    {
        $this->includeEvaluationReport();
        $this->assertEquals(3, count($this->transcriptTable->clientTranscriptTables));
        $this->assertInstanceOf(ClientTranscriptTable::class, $this->transcriptTable->clientTranscriptTables[2]);
    }
    public function test_includeEvaluationReport_includeInCorrepondingClientTranscriptTable()
    {
        $this->clientTranscriptTableTwo->expects($this->once())
                ->method('canInclude')
                ->with($this->evaluationReport)
                ->willReturn(true);
        $this->clientTranscriptTableTwo->expects($this->once())
                ->method('includeEvaluationReport')
                ->with($this->evaluationReport);
        $this->includeEvaluationReport();
    }
    public function test_includeEvaluationReport_containTableCorrespondWithReport_preventAddNewTable()
    {
        $this->clientTranscriptTableTwo->expects($this->once())
                ->method('canInclude')
                ->with($this->evaluationReport)
                ->willReturn(true);
        $this->includeEvaluationReport();
        $this->assertEquals(2, count($this->transcriptTable->clientTranscriptTables));
    }
    
    protected function saveToSpreadsheet()
    {
        $this->spreadsheet->expects($this->any())
                ->method('createSheet')
                ->willReturn($this->worksheet);
        $this->transcriptTable->saveToSpreadsheet($this->spreadsheet);
    }
    public function test_saveToSpreadsheet_createSheetAndSetTitle()
    {
        $this->spreadsheet->expects($this->any())
                ->method('createSheet')
                ->willReturn($this->worksheet);
        $this->worksheet->expects($this->once())
                ->method('setTitle')
                ->with($this->clientName);
        $this->saveToSpreadsheet();
    }
    public function test_saveToSpreadsheet_saveAllClientTranscriptTableToWorksheet()
    {
        $this->clientTranscriptTableOne->expects($this->once())
                ->method('toSimplifiedTranscriptFormatArray')
                ->willReturn($transcriptOneTable = [
                    ['mentor', 'mentor one name', 'mentor two name'],
                    ['field one label', 'field 11 value', 'field 21 value'],
                    ['field two label', 'field 12 value', 'field 22 value'],
                ]);
        $this->clientTranscriptTableTwo->expects($this->once())
                ->method('toSimplifiedTranscriptFormatArray')
                ->willReturn($transcriptTwoTable = [
                    ['mentor', 'mentor one name', 'mentor two name'],
                    ['field one label', 'field 11 value', 'field 21 value'],
                    ['field two label', 'field 12 value', 'field 22 value'],
                ]);
        $transcriptTable = [
            [$this->evaluationPlanOneName],
            ['mentor', 'mentor one name', 'mentor two name'],
            ['field one label', 'field 11 value', 'field 21 value'],
            ['field two label', 'field 12 value', 'field 22 value'],
            [],
            [$this->evaluationPlanTwoName],
            ['mentor', 'mentor one name', 'mentor two name'],
            ['field one label', 'field 11 value', 'field 21 value'],
            ['field two label', 'field 12 value', 'field 22 value'],
        ];
        $this->worksheet->expects($this->once())
                ->method('fromArray')
                ->with($transcriptTable);
        $this->saveToSpreadsheet();
    }
    
    protected function toRelationalArray()
    {
        return $this->transcriptTable->toRelationalArray();
    }
    public function test_toRelationalArray_returnAllClientTranscriptTableRelationalArray()
    {
        $this->clientTranscriptTableOne->expects($this->once())
                ->method('toRelationalArray')
                ->willReturn($clientTranscriptTableOneArray = [
                    'id' => 'evaluation-plan-one-id',
                    'name' => 'evaluation plan one name',
                    'summaryTable' => [
                        'header' => ['mentor', 'field one label', 'field two label'],
                        'entries' => [
                            ['mentor one name', 'field 11 value', 'field 12value'],
                            ['mentor two name', 'field 21 value', 'field 22value'],
                        ],
                    ],
                ]);
        $this->clientTranscriptTableTwo->expects($this->once())
                ->method('toRelationalArray')
                ->willReturn($clientTranscriptTableTwoArray = [
                    'id' => 'evaluation-plan-one-id',
                    'name' => 'evaluation plan two name',
                    'summaryTable' => [
                        'header' => ['mentor', 'field one label', 'field two label'],
                        'entries' => [
                            ['mentor one name', 'field 11 value', 'field 12value'],
                            ['mentor two name', 'field 21 value', 'field 22value'],
                        ],
                    ],
                ]);
        $this->assertEquals([
            'id' => $this->clientId,
            'name' => $this->clientName,
            'evaluationPlans' => [
                $clientTranscriptTableOneArray,
                $clientTranscriptTableTwoArray,
            ],
        ], $this->transcriptTable->toRelationalArray());
    }
}

class TestableTranscriptTable extends TranscriptTable
{
    public $client;
    public $clientTranscriptTables;
}

<?php

namespace Query\Domain\Model\Firm\Program\Participant;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Query\Domain\Model\Firm\Program\EvaluationPlan;
use Query\Domain\Model\Firm\Program\EvaluationPlan\ParticipantTranscriptTable;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Tests\TestBase;

class TranscriptTableTest extends TestBase
{
    protected $participant, $participantId = 'participant-id', $participantName = 'participant name', 
            $programName = 'program name', $teamName = 'team name', $programId = 'programId';
    protected $transcriptTable;
    protected $participantTranscriptTableOne, $evaluationPlanOneName = 'evaluation plan one name';
    protected $participantTranscriptTableTwo, $evaluationPlanTwoName = 'evaluation plan two name';
    
    protected $evaluationReport, $evaluationPlan;
    protected $spreadsheet, $worksheet;
    protected $summaryStyleView = false;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->evaluationReport = $this->buildMockOfClass(EvaluationReport::class);
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->participant->expects($this->any())->method('getId')->willReturn($this->participantId);
        $this->participant->expects($this->any())->method('getName')->willReturn($this->participantName);
        
        $this->transcriptTable = new TestableTranscriptTable($this->evaluationReport);
        $this->transcriptTable->participant = $this->participant;
        $this->transcriptTable->participantTranscriptTables = [];
        
        $this->participantTranscriptTableOne = $this->buildMockOfClass(ParticipantTranscriptTable::class);
        $this->participantTranscriptTableOne->expects($this->any())->method('getEvaluationPlanName')->willReturn($this->evaluationPlanOneName);
        $this->participantTranscriptTableTwo = $this->buildMockOfClass(ParticipantTranscriptTable::class);
        $this->participantTranscriptTableTwo->expects($this->any())->method('getEvaluationPlanName')->willReturn($this->evaluationPlanTwoName);
        
        $this->transcriptTable->participantTranscriptTables[] = $this->participantTranscriptTableOne;
        $this->transcriptTable->participantTranscriptTables[] = $this->participantTranscriptTableTwo;
        
        
        $this->spreadsheet = $this->buildMockOfClass(Spreadsheet::class);
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
    }
    
    public function test_construct_setParticipantFromEvaluationReport()
    {
        $this->evaluationReport->expects($this->once())
                ->method('getParticipant')
                ->willReturn($this->participant);
        
        $transcriptTable = new TestableTranscriptTable($this->evaluationReport);
        $this->assertEquals($this->participant, $transcriptTable->participant);
    }
    public function test_construct_addInitialParticipantTranscriptTable()
    {
        $transcriptTable = new TestableTranscriptTable($this->evaluationReport);
        $this->assertInstanceOf(ParticipantTranscriptTable::class, $transcriptTable->participantTranscriptTables[0]);
    }
    
    public function test_canInclude_returnEvaluationReportCorrespondWithParticipantStatusResult()
    {
        $this->evaluationReport->expects($this->once())
                ->method('correspondWithParticipant')
                ->with($this->participant);
        $this->transcriptTable->canInclude($this->evaluationReport);
    }
    
    protected function includeEvaluationReport()
    {
        $this->transcriptTable->includeEvaluationReport($this->evaluationReport);
    }
    public function test_includeEvaluationReport_addNewParticipantTranscriptTable()
    {
        $this->includeEvaluationReport();
        $this->assertEquals(3, count($this->transcriptTable->participantTranscriptTables));
        $this->assertInstanceOf(ParticipantTranscriptTable::class, $this->transcriptTable->participantTranscriptTables[2]);
    }
    public function test_includeEvaluationReport_hasTableCorrespondWithEvaluationReport_includeInExistingTable()
    {
        $this->participantTranscriptTableTwo->expects($this->once())
                ->method('canInclude')
                ->with($this->evaluationReport)
                ->willReturn(true);
        $this->participantTranscriptTableTwo->expects($this->once())
                ->method('includeEvaluationReport')
                ->with($this->evaluationReport);
        $this->includeEvaluationReport();
    }
    public function test_includeEvaluationReport_hasTableCorrespondWithEvaluationReport_preventAddNewTable()
    {
        $this->participantTranscriptTableTwo->expects($this->once())
                ->method('canInclude')
                ->with($this->evaluationReport)
                ->willReturn(true);
        $this->includeEvaluationReport();
        $this->assertEquals(2, count($this->transcriptTable->participantTranscriptTables));
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
                ->with($this->participantName);
        $this->saveToSpreadsheet();
    }
    public function test_saveToSpreadsheet_saveAllParticipantTranscriptTableToWorksheet()
    {
        $this->participantTranscriptTableOne->expects($this->once())
                ->method('toSimplifiedTranscriptFormatArray')
                ->willReturn($transcriptOneTable = [
                    ['mentor', 'mentor one name', 'mentor two name'],
                    ['field one label', 'field 11 value', 'field 21 value'],
                    ['field two label', 'field 12 value', 'field 22 value'],
                ]);
        $this->participantTranscriptTableTwo->expects($this->once())
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
    public function test_toRelationalArray_returnAllParticipantTranscriptTableRelationalArray()
    {
        $this->participantTranscriptTableOne->expects($this->once())
                ->method('toRelationalArray')
                ->willReturn($evaluationPlanOneTable = [
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
        $this->participantTranscriptTableTwo->expects($this->once())
                ->method('toRelationalArray')
                ->willReturn($evaluationPlanTwoTable = [
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
            'id' => $this->participantId,
            'name' => $this->participantName,
            'evaluationPlans' => [
                $evaluationPlanOneTable,
                $evaluationPlanTwoTable,
            ],
        ], $this->transcriptTable->toRelationalArray());
    }
    
    protected function saveAsProgramSheet()
    {
        $this->spreadsheet->expects($this->any())
                ->method('createSheet')
                ->willReturn($this->worksheet);
        
        $this->participant->expects($this->any())->method('getProgramName')->willReturn($this->programName);
        $this->participant->expects($this->any())->method('getTeamName')->willReturn($this->teamName);
        $this->transcriptTable->saveAsProgramSheet($this->spreadsheet, $this->summaryStyleView);
    }
    public function test_saveAsProgramSheet_createSheetAndSetTitle()
    {
        $this->worksheet->expects($this->once())
                ->method('setTitle')
                ->with("{$this->programName} - {$this->teamName}");
        $this->saveAsProgramSheet();
    }
    public function test_saveAsProgramSheet_containInvalidSheetCharacter_removeCharacter()
    {
        $this->programName = 'program&#$#$^ name';
        $this->teamName = 'team &@#%@#@#!#@#@)name';
        $this->worksheet->expects($this->once())
                ->method('setTitle')
                ->with("program name - team name");
        $this->saveAsProgramSheet();
    }
    public function test_saveAsProgramSheet_emptyTeamName_setProgramAsTitle()
    {
        $this->teamName = '';
        $this->worksheet->expects($this->once())
                ->method('setTitle')
                ->with($this->programName);
        $this->saveAsProgramSheet();
    }
    public function test_saveAsProgramSheet_saveAllParticipantTranscriptTableToWorksheet()
    {
        $this->participantTranscriptTableOne->expects($this->once())
                ->method('toSimplifiedTranscriptFormatArray')
                ->willReturn($transcriptOneTable = [
                    ['mentor', 'mentor one name', 'mentor two name'],
                    ['field one label', 'field 11 value', 'field 21 value'],
                    ['field two label', 'field 12 value', 'field 22 value'],
                ]);
        $this->participantTranscriptTableTwo->expects($this->once())
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
        $this->saveAsProgramSheet();
    }
    
    protected function toRelationalArrayOfProgram()
    {
        $this->participant->expects($this->any())->method('getProgramName')->willReturn($this->programName);
        $this->participant->expects($this->any())->method('getProgramId')->willReturn($this->programId);
        return $this->transcriptTable->toRelationalArrayOfProgram();
    }
    public function test_toRelationalArrayOfProgram_returnAllParticipantTranscriptTableRelationalArrayOfProgram()
    {
        $this->participantTranscriptTableOne->expects($this->once())
                ->method('toRelationalArray')
                ->willReturn($evaluationPlanOneTable = [
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
        $this->participantTranscriptTableTwo->expects($this->once())
                ->method('toRelationalArray')
                ->willReturn($evaluationPlanTwoTable = [
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
            'programId' => $this->programId,
            'programName' => $this->programName,
            'evaluationPlans' => [
                $evaluationPlanOneTable,
                $evaluationPlanTwoTable,
            ],
        ], $this->toRelationalArrayOfProgram());
    }
}

class TestableTranscriptTable extends TranscriptTable
{
    public $participant;
    public $participantTranscriptTables;
}

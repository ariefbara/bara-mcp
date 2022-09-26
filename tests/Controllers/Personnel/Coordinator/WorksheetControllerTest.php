<?php

namespace Tests\Controllers\Personnel\Coordinator;

use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfWorksheet;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfWorksheetForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class WorksheetControllerTest extends ExtendedCoordinatorTestCase
{

    protected $worksheetUri;
    protected $worksheetOne;
    protected $worksheetTwo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheetUri = $this->coordinatorUri . "/worksheets";

        $this->connection->table('Form')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('Worksheet')->truncate();

        $program = $this->coordinator->program;
        $firm = $program->firm;

        $formOne = new RecordOfForm(1);
        $formTwo = new RecordOfForm(2);

        $worksheetFormOne = new RecordOfWorksheetForm($firm, $formOne);
        $worksheetFormTwo = new RecordOfWorksheetForm($firm, $formTwo);

        $participantOne = new RecordOfParticipant($program, 1);
        $participantTwo = new RecordOfParticipant($program, 2);

        $formRecordOne = new RecordOfFormRecord($formOne, 1);
        $formRecordTwo = new RecordOfFormRecord($formTwo, 2);

        $missionOne = new RecordOfMission($program, $worksheetFormOne, 1, null);
        $missionTwo = new RecordOfMission($program, $worksheetFormTwo, 2, null);

        $this->worksheetOne = new RecordOfWorksheet($participantOne, $formRecordOne, $missionOne, 1);
        $this->worksheetTwo = new RecordOfWorksheet($participantTwo, $formRecordTwo, $missionTwo, 2);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Form')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('Worksheet')->truncate();
    }
    
    protected function viewAll()
    {
        $this->persistCoordinatorDependency();
        
        $this->worksheetOne->mission->worksheetForm->form->insert($this->connection);
        $this->worksheetTwo->mission->worksheetForm->form->insert($this->connection);
        
        $this->worksheetOne->mission->worksheetForm->insert($this->connection);
        $this->worksheetTwo->mission->worksheetForm->insert($this->connection);
        
        $this->worksheetOne->mission->insert($this->connection);
        $this->worksheetTwo->mission->insert($this->connection);
        
        $this->worksheetOne->participant->insert($this->connection);
        $this->worksheetTwo->participant->insert($this->connection);
        
        $this->worksheetOne->insert($this->connection);
        $this->worksheetTwo->insert($this->connection);
        
        $this->get($this->worksheetUri, $this->coordinator->personnel->token);
    }
    public function test_viewAll_200()
    {
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 2,
            'list' => [
                [
                    'id' => $this->worksheetOne->id,
                    'name' => $this->worksheetOne->name,
                    'mission' => [
                        'id' => $this->worksheetOne->mission->id,
                        'name' => $this->worksheetOne->mission->name,
                        'description' => $this->worksheetOne->mission->description,
                        'worksheetForm' => [
                            'id' => $this->worksheetOne->mission->worksheetForm->id,
                            'name' => $this->worksheetOne->mission->worksheetForm->form->name,
                            'description' => $this->worksheetOne->mission->worksheetForm->form->description,
                        ],
                    ],
                    'parent' => null
                ],
                [
                    'id' => $this->worksheetTwo->id,
                    'name' => $this->worksheetTwo->name,
                    'mission' => [
                        'id' => $this->worksheetTwo->mission->id,
                        'name' => $this->worksheetTwo->mission->name,
                        'description' => $this->worksheetTwo->mission->description,
                        'worksheetForm' => [
                            'id' => $this->worksheetTwo->mission->worksheetForm->id,
                            'name' => $this->worksheetTwo->mission->worksheetForm->form->name,
                            'description' => $this->worksheetTwo->mission->worksheetForm->form->description,
                        ],
                    ],
                    'parent' => null
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewAll_useParticipantIdFilter()
    {
        $this->worksheetUri .= "?participantId={$this->worksheetOne->participant->id}";
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 1]);
        $this->seeJsonContains(['id' => $this->worksheetOne->id]);
        $this->seeJsonDoesntContains(['id' => $this->worksheetTwo->id]);
    }
    public function test_viewAll_excludeWorksheetOfOtherProgramParticipant()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        $this->worksheetOne->participant->program = $otherProgram;
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 1]);
        $this->seeJsonDoesntContains(['id' => $this->worksheetOne->id]);
        $this->seeJsonContains(['id' => $this->worksheetTwo->id]);
    }
    public function test_viewAll_inactiveCoordinator_403()
    {
        $this->coordinator->active = false;
        
        $this->viewAll();
        $this->seeStatusCode(403);
    }
    
    protected function viewDetail()
    {
        $this->persistCoordinatorDependency();
        
        $this->worksheetOne->mission->worksheetForm->form->insert($this->connection);
        $this->worksheetOne->mission->worksheetForm->insert($this->connection);
        $this->worksheetOne->mission->insert($this->connection);
        $this->worksheetOne->participant->insert($this->connection);
        $this->worksheetOne->insert($this->connection);
        
        $uri = $this->worksheetUri . "/{$this->worksheetOne->id}";
        $this->get($uri, $this->coordinator->personnel->token);
    }
    public function test_viewDetail_200()
    {
        $this->viewDetail();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->worksheetOne->id,
            'name' => $this->worksheetOne->name,
            "submitTime" => $this->worksheetOne->formRecord->submitTime,
            "stringFieldRecords" => [],
            "integerFieldRecords" => [],
            "textAreaFieldRecords" => [],
            "attachmentFieldRecords" => [],
            "singleSelectFieldRecords" => [],
            "multiSelectFieldRecords" => [],
            'mission' => [
                'id' => $this->worksheetOne->mission->id,
                'name' => $this->worksheetOne->mission->name,
                'description' => $this->worksheetOne->mission->description,
                'worksheetForm' => [
                    'id' => $this->worksheetOne->mission->worksheetForm->id,
                    'name' => $this->worksheetOne->mission->worksheetForm->form->name,
                    'description' => $this->worksheetOne->mission->worksheetForm->form->description,
                ],
            ],
            'parent' => null
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewDetail_inactiveCoordinator_403()
    {
        $this->coordinator->active = false;
        
        $this->viewDetail();
        $this->seeStatusCode(403);
    }
    public function test_viewDetail_unmanagedWorksheet_belongsToParticipantOfOtherProgram_404()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        $this->worksheetOne->participant->program = $otherProgram;
        
        $this->viewDetail();
        $this->seeStatusCode(404);
    }

}

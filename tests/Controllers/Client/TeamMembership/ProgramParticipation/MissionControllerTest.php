<?php

namespace Tests\Controllers\Client\TeamMembership\ProgramParticipation;

use Tests\Controllers\ {
    Client\TeamMembership\ProgramParticipationTestCase,
    RecordPreparation\Firm\Program\Participant\RecordOfWorksheet,
    RecordPreparation\Firm\Program\RecordOfMission,
    RecordPreparation\Firm\RecordOfWorksheetForm,
    RecordPreparation\Shared\RecordOfForm,
    RecordPreparation\Shared\RecordOfFormRecord
};

class MissionControllerTest extends ProgramParticipationTestCase
{
    protected $missionUri;
    protected $mission_0;
    protected $mission_1;
    protected $mission_2;
    
    protected $worksheet_00;
    protected $worksheet_01;
    protected $worksheet_10;
    protected $worksheet_20;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->missionUri = $this->programParticipationUri . "/{$this->programParticipation->id}/missions";
        $this->connection->table('Form')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('Worksheet')->truncate();
        
        $participant = $this->programParticipation->participant;
        $program = $participant->program;
        $firm = $program->firm;
        
        $form = new RecordOfForm(0);
        $this->connection->table('Form')->insert($form->toArrayForDbEntry());
        
        $worksheetForm = new RecordOfWorksheetForm($firm, $form);
        $this->connection->table('WorksheetForm')->insert($worksheetForm->toArrayForDbEntry());
        
        $this->mission_0 = new RecordOfMission($program, $worksheetForm, 0, null);
        $this->mission_0->published = true;
        $this->mission_1 = new RecordOfMission($program, $worksheetForm, 1, $this->mission_0);
        $this->mission_1->published = true;
        $this->mission_2 = new RecordOfMission($program, $worksheetForm, 2, $this->mission_1);
        $this->mission_2->published = true;
        $this->connection->table('Mission')->insert($this->mission_0->toArrayForDbEntry());
        $this->connection->table('Mission')->insert($this->mission_1->toArrayForDbEntry());
        $this->connection->table('Mission')->insert($this->mission_2->toArrayForDbEntry());
        
        $formRecord_00 = new RecordOfFormRecord($form, "00");
        $formRecord_01 = new RecordOfFormRecord($form, "01");
        $formRecord_10 = new RecordOfFormRecord($form, "10");
        $formRecord_20 = new RecordOfFormRecord($form, "20");
        $this->connection->table('FormRecord')->insert($formRecord_00->toArrayForDbEntry());
        $this->connection->table('FormRecord')->insert($formRecord_01->toArrayForDbEntry());
        $this->connection->table('FormRecord')->insert($formRecord_10->toArrayForDbEntry());
        $this->connection->table('FormRecord')->insert($formRecord_20->toArrayForDbEntry());
        
        $this->worksheet_00 = new RecordOfWorksheet($participant, $formRecord_00, $this->mission_0, "00");
        $this->worksheet_01 = new RecordOfWorksheet($participant, $formRecord_01, $this->mission_0, "01");
        $this->worksheet_01->removed = true;
        $this->worksheet_10 = new RecordOfWorksheet($participant, $formRecord_10, $this->mission_1, "10");
        $this->worksheet_10->parent = $this->worksheet_00;
        $this->worksheet_20 = new RecordOfWorksheet($participant, $formRecord_20, $this->mission_2, "20");
        $this->worksheet_20->parent = $this->worksheet_10;
        $this->connection->table('Worksheet')->insert($this->worksheet_00->toArrayForDbEntry());
        $this->connection->table('Worksheet')->insert($this->worksheet_01->toArrayForDbEntry());
        $this->connection->table('Worksheet')->insert($this->worksheet_10->toArrayForDbEntry());
        $this->connection->table('Worksheet')->insert($this->worksheet_20->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
//        parent::tearDown();
//        $this->connection->table('Form')->truncate();
//        $this->connection->table('WorksheetForm')->truncate();
//        $this->connection->table('Mission')->truncate();
//        $this->connection->table('FormRecord')->truncate();
//        $this->connection->table('Worksheet')->truncate();
    }
    
    public function test_showAll()
    {
        $response = [
            'list' => [
                [
                    'id' => $this->mission_0->id,
                    'name' => $this->mission_0->name,
                    'description' => $this->mission_0->description,
                    'position' => $this->mission_0->position,
                    'submittedWorksheet' => "1",
                ],
                [
                    'id' => $this->mission_1->id,
                    'name' => $this->mission_1->name,
                    'description' => $this->mission_1->description,
                    'position' => $this->mission_1->position,
                    'submittedWorksheet' => "1",
                ],
                [
                    'id' => $this->mission_2->id,
                    'name' => $this->mission_2->name,
                    'description' => $this->mission_2->description,
                    'position' => $this->mission_2->position,
                    'submittedWorksheet' => '1',
                ],
            ],
        ];
        $this->get($this->missionUri, $this->teamMembership->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_showAll_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        $this->get($this->missionUri, $this->teamMembership->client->token)
                ->seeStatusCode(403);
        
    }
    
    public function test_show()
    {
        $response = [
            'id' => $this->mission_2->id,
            'name' => $this->mission_2->name,
            'description' => $this->mission_2->description,
            'position' => $this->mission_2->position,
            'parent' => [
                'id' => $this->mission_2->parent->id,
                'name' => $this->mission_2->parent->name,
                'description' => $this->mission_2->parent->description,
                'position' => $this->mission_2->parent->position,
                'parent' => [
                    'id' => $this->mission_2->parent->parent->id,
                    'name' => $this->mission_2->parent->parent->name,
                    'description' => $this->mission_2->parent->parent->description,
                    'position' => $this->mission_2->parent->parent->position,
                    'parent' => null
                ],
            ],
            'worksheetForm' => [
                'id' => $this->mission_2->worksheetForm->id,
                'name' => $this->mission_2->worksheetForm->form->name,
                'description' => $this->mission_2->worksheetForm->form->description,
                'stringFields' => [],
                'integerFields' => [],
                'textAreaFields' => [],
                'attachmentFields' => [],
                'singleSelectFields' => [],
                'multiSelectFields' => [],
            ],
        ];
        $uri = $this->missionUri . "/by-id/{$this->mission_2->id}";
        $this->get($uri, $this->teamMembership->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_show_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        $uri = $this->missionUri . "/by-id/{$this->mission_2->id}";
        $this->get($uri, $this->teamMembership->client->token)
                ->seeStatusCode(403);
    }
    
    public function test_showByPosition()
    {
        $response = [
            'id' => $this->mission_2->id,
            'name' => $this->mission_2->name,
            'description' => $this->mission_2->description,
            'position' => $this->mission_2->position,
            'parent' => [
                'id' => $this->mission_2->parent->id,
                'name' => $this->mission_2->parent->name,
                'description' => $this->mission_2->parent->description,
                'position' => $this->mission_2->parent->position,
                'parent' => [
                    'id' => $this->mission_2->parent->parent->id,
                    'name' => $this->mission_2->parent->parent->name,
                    'description' => $this->mission_2->parent->parent->description,
                    'position' => $this->mission_2->parent->parent->position,
                    'parent' => null
                ],
            ],
            'worksheetForm' => [
                'id' => $this->mission_2->worksheetForm->id,
                'name' => $this->mission_2->worksheetForm->form->name,
                'description' => $this->mission_2->worksheetForm->form->description,
                'stringFields' => [],
                'integerFields' => [],
                'textAreaFields' => [],
                'attachmentFields' => [],
                'singleSelectFields' => [],
                'multiSelectFields' => [],
            ],
        ];
        $uri = $this->missionUri . "/by-position/{$this->mission_2->position}";
        $this->get($uri, $this->teamMembership->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
        
    }
    public function test_showByPosition_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        $uri = $this->missionUri . "/by-position/{$this->mission_2->position}";
        $this->get($uri, $this->teamMembership->client->token)
                ->seeStatusCode(403);
    }
}
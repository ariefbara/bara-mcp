<?php

namespace Tests\Controllers\Client\AsProgramParticipant;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\Participant\RecordOfWorksheet,
    Firm\Program\RecordOfMission,
    Firm\RecordOfProgram,
    Firm\RecordOfWorksheetForm,
    Shared\RecordOfForm,
    Shared\RecordOfFormRecord
};

class MissionControllerTest extends MissionTestCase
{
    protected $mission_0;
    protected $mission_1;
    protected $mission_2;
    protected $mission_3_otherProgram;


    protected $worksheet_00;
    protected $worksheet_01;
    protected $worksheet_10_removed;
    protected $worksheet_20;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Form')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('Worksheet')->truncate();
        
        $participant = $this->programParticipation->participant;
        $program = $participant->program;
        $firm = $program->firm;
        
        $form = new RecordOfForm(0);
        $this->connection->table('Form')->insert($form->toArrayForDbEntry());
        
        $worksheetForm = new RecordOfWorksheetForm($firm, $form);
        $this->connection->table('WorksheetForm')->insert($worksheetForm->toArrayForDbEntry());
        
        $otherProgram = new RecordOfProgram($firm, 1);
        $this->connection->table('Program')->insert($otherProgram->toArrayForDbEntry());
        
        $this->mission_0 = new RecordOfMission($program, $worksheetForm, 0, null);
        $this->mission_0->published = true;
        $this->mission_1 = new RecordOfMission($program, $worksheetForm, 1, $this->mission_0);
        $this->mission_1->published = true;
        $this->mission_2 = new RecordOfMission($program, $worksheetForm, 2, $this->mission_1);
        $this->mission_2->published = true;
        $this->mission_3_otherProgram = new RecordOfMission($otherProgram, $worksheetForm, 3, null);
        $this->mission_3_otherProgram->published = true;
        $this->connection->table('Mission')->insert($this->mission_0->toArrayForDbEntry());
        $this->connection->table('Mission')->insert($this->mission_1->toArrayForDbEntry());
        $this->connection->table('Mission')->insert($this->mission_2->toArrayForDbEntry());
        $this->connection->table('Mission')->insert($this->mission_3_otherProgram->toArrayForDbEntry());
        
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
        $this->worksheet_10_removed = new RecordOfWorksheet($participant, $formRecord_10, $this->mission_1, "10");
        $this->worksheet_10_removed->parent = $this->worksheet_00;
        $this->worksheet_10_removed->removed = true;
        $this->worksheet_20 = new RecordOfWorksheet($participant, $formRecord_20, $this->mission_2, "20");
        $this->worksheet_20->parent = $this->worksheet_10_removed;
        $this->connection->table('Worksheet')->insert($this->worksheet_00->toArrayForDbEntry());
        $this->connection->table('Worksheet')->insert($this->worksheet_01->toArrayForDbEntry());
        $this->connection->table('Worksheet')->insert($this->worksheet_10_removed->toArrayForDbEntry());
        $this->connection->table('Worksheet')->insert($this->worksheet_20->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Form')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('Worksheet')->truncate();
    }
    
    public function test_showAll()
    {
        $response = [
            'list' => [
                [
                    'id' => $this->mission->id,
                    'name' => $this->mission->name,
                    'description' => $this->mission->description,
                    'position' => $this->mission->position,
                    'worksheetForm' => [
                        'id' => $this->mission->worksheetForm->id,
                        'name' => $this->mission->worksheetForm->form->name,
                    ],
                    'submittedWorksheet' => null,
                ],
                [
                    'id' => $this->mission_0->id,
                    'name' => $this->mission_0->name,
                    'description' => $this->mission_0->description,
                    'position' => $this->mission_0->position,
                    'worksheetForm' => [
                        'id' => $this->mission_0->worksheetForm->id,
                        'name' => $this->mission_0->worksheetForm->form->name,
                    ],
                    'submittedWorksheet' => "2",
                ],
                [
                    'id' => $this->mission_1->id,
                    'name' => $this->mission_1->name,
                    'description' => $this->mission_1->description,
                    'position' => $this->mission_1->position,
                    'worksheetForm' => [
                        'id' => $this->mission_1->worksheetForm->id,
                        'name' => $this->mission_1->worksheetForm->form->name,
                    ],
                    'submittedWorksheet' => null,
                ],
                [
                    'id' => $this->mission_2->id,
                    'name' => $this->mission_2->name,
                    'description' => $this->mission_2->description,
                    'position' => $this->mission_2->position,
                    'worksheetForm' => [
                        'id' => $this->mission_2->worksheetForm->id,
                        'name' => $this->mission_2->worksheetForm->form->name,
                    ],
                    'submittedWorksheet' => '1',
                ],
            ],
        ];
        $this->get($this->missionUri, $this->programParticipation->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_showAll_paginationApply()
    {
        $response = [
            "total" => 4,
            "list" => [
                [
                    'id' => $this->mission_2->id,
                    'name' => $this->mission_2->name,
                    'description' => $this->mission_2->description,
                    'position' => $this->mission_2->position,
                    'worksheetForm' => [
                        'id' => $this->mission_2->worksheetForm->id,
                        'name' => $this->mission_2->worksheetForm->form->name,
                    ],
                    'submittedWorksheet' => '1',
                ],
                [
                    'id' => $this->mission->id,
                    'name' => $this->mission->name,
                    'description' => $this->mission->description,
                    'position' => $this->mission->position,
                    'worksheetForm' => [
                        'id' => $this->mission->worksheetForm->id,
                        'name' => $this->mission->worksheetForm->form->name,
                    ],
                    'submittedWorksheet' => null,
                ],
            ],
        ];
        $uri = $this->missionUri . "?page=2&pageSize=2";
        $this->get($uri, $this->programParticipation->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_inactiveParticipant_403()
    {
        $this->setInactiveParticipant();
        $this->get($this->missionUri, $this->programParticipation->client->token)
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
        $this->get($uri, $this->programParticipation->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_show_missionWithoutWorksheet_200_bug20230519()
    {
$this->disableExceptionHandling();
        $this->connection->table('Mission')->truncate();
        $this->mission_2->worksheetForm = null;
        $this->mission_0->insert($this->connection);
        $this->mission_1->insert($this->connection);
        $this->mission_2->insert($this->connection);
        
        $uri = $this->missionUri . "/by-id/{$this->mission_2->id}";
        $this->get($uri, $this->programParticipation->client->token);
        $this->seeStatusCode(200);
        $this->seeJsonContains([
            'id' => $this->mission_2->id,
            'worksheetForm' => null,
        ]);
    }
    public function test_show_inactiveParticipant_403()
    {
        $this->setInactiveParticipant();
        $uri = $this->missionUri . "/by-id/{$this->mission_2->id}";
        $this->get($uri, $this->programParticipation->client->token)
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
        $this->get($uri, $this->programParticipation->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
        
    }
    public function test_showByPosition_inactiveParticipant_403()
    {
        $this->setInactiveParticipant();
        $uri = $this->missionUri . "/by-position/{$this->mission_2->position}";
        $this->get($uri, $this->programParticipation->client->token)
                ->seeStatusCode(403);
    }
}

<?php

namespace Tests\Controllers\Client\ProgramParticipation;

use Tests\Controllers\ {
    Client\ProgramParticipationTestCase,
    RecordPreparation\Firm\Program\Participant\RecordOfWorksheet,
    RecordPreparation\Firm\Program\RecordOfMission,
    RecordPreparation\Firm\RecordOfWorksheetForm,
    RecordPreparation\Shared\RecordOfForm
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
        
        $form = new RecordOfForm(0);
        $this->connection->table('Form')->insert($form->toArrayForDbEntry());
        
        $worksheetForm = new RecordOfWorksheetForm($this->programParticipation->program->firm, $form);
        $this->connection->table('WorksheetForm')->insert($worksheetForm->toArrayForDbEntry());
        
        $this->mission_0 = new RecordOfMission($this->programParticipation->program, $worksheetForm, 0, null);
        $this->mission_0->published = true;
        $this->mission_1 = new RecordOfMission($this->programParticipation->program, $worksheetForm, 1, $this->mission_0);
        $this->mission_1->published = true;
        $this->mission_2 = new RecordOfMission($this->programParticipation->program, $worksheetForm, 2, $this->mission_1);
        $this->mission_2->published = true;
        $this->connection->table('Mission')->insert($this->mission_0->toArrayForDbEntry());
        $this->connection->table('Mission')->insert($this->mission_1->toArrayForDbEntry());
        $this->connection->table('Mission')->insert($this->mission_2->toArrayForDbEntry());
        
        $formRecord_00 = new \Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord($form, "00");
        $formRecord_01 = new \Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord($form, "01");
        $formRecord_10 = new \Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord($form, "10");
        $formRecord_20 = new \Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord($form, "20");
        $this->connection->table('FormRecord')->insert($formRecord_00->toArrayForDbEntry());
        $this->connection->table('FormRecord')->insert($formRecord_01->toArrayForDbEntry());
        $this->connection->table('FormRecord')->insert($formRecord_10->toArrayForDbEntry());
        $this->connection->table('FormRecord')->insert($formRecord_20->toArrayForDbEntry());
        
        $this->worksheet_00 = new RecordOfWorksheet($this->programParticipation, $formRecord_00, $this->mission_0);
        $this->worksheet_01 = new RecordOfWorksheet($this->programParticipation, $formRecord_01, $this->mission_0);
        $this->worksheet_01->removed = true;
        $this->worksheet_10 = new RecordOfWorksheet($this->programParticipation, $formRecord_10, $this->mission_1);
        $this->worksheet_10->parent = $this->worksheet_00;
        $this->worksheet_20 = new RecordOfWorksheet($this->programParticipation, $formRecord_20, $this->mission_2);
        $this->worksheet_20->parent = $this->worksheet_10;
        $this->connection->table('Worksheet')->insert($this->worksheet_00->toArrayForDbEntry());
        $this->connection->table('Worksheet')->insert($this->worksheet_01->toArrayForDbEntry());
        $this->connection->table('Worksheet')->insert($this->worksheet_10->toArrayForDbEntry());
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
        $this->get($this->missionUri, $this->programParticipation->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
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
                'parent' => [
                    'id' => $this->mission_2->parent->parent->id,
                    'name' => $this->mission_2->parent->parent->name,
                    'parent' => null
                ],
            ],
            'worksheets' => [
                [
                    'id' => $this->worksheet_20->id,
                    'name' => $this->worksheet_20->name,
                    'parent' => [
                        'id' => $this->worksheet_20->parent->id,
                        'name' => $this->worksheet_20->parent->name,
                        'parent' => [
                            'id' => $this->worksheet_20->parent->parent->id,
                            'name' => $this->worksheet_20->parent->parent->name,
                            'parent' => null
                        ],
                    ],
                ],
            ],
        ];
        $uri = $this->missionUri . "/by-id/{$this->mission_2->id}";
        $this->get($uri, $this->programParticipation->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    
    public function test_showByPosition()
    {
$this->disableExceptionHandling();
        $response = [
            'id' => $this->mission_2->id,
            'name' => $this->mission_2->name,
            'description' => $this->mission_2->description,
            'position' => $this->mission_2->position,
            'parent' => [
                'id' => $this->mission_2->parent->id,
                'name' => $this->mission_2->parent->name,
                'parent' => [
                    'id' => $this->mission_2->parent->parent->id,
                    'name' => $this->mission_2->parent->parent->name,
                    'parent' => null
                ],
            ],
            'worksheets' => [
                [
                    'id' => $this->worksheet_20->id,
                    'name' => $this->worksheet_20->name,
                    'parent' => [
                        'id' => $this->worksheet_20->parent->id,
                        'name' => $this->worksheet_20->parent->name,
                        'parent' => [
                            'id' => $this->worksheet_20->parent->parent->id,
                            'name' => $this->worksheet_20->parent->parent->name,
                            'parent' => null
                        ],
                    ],
                ],
            ],
        ];
        $uri = $this->missionUri . "/by-position/{$this->mission_2->position}";
        $this->get($uri, $this->programParticipation->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
        
    }
}

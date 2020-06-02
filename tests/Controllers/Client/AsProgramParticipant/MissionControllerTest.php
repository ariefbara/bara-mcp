<?php

namespace Tests\Controllers\Client\AsProgramParticipant;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfMission,
    Firm\RecordOfWorksheetForm,
    Shared\RecordOfForm
};

class MissionControllerTest extends MissionTestCase
{
    protected $missionOne;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $form = new RecordOfForm(1);
        $this->connection->table('Form')->insert($form->toArrayForDbEntry());
        
        $worksheetForm = new RecordOfWorksheetForm($this->participant->program->firm, $form);
        $this->connection->table('WorksheetForm')->insert($worksheetForm->toArrayForDbEntry());
        
        $this->missionOne = new RecordOfMission($this->participant->program, $worksheetForm, 1, $this->mission);
        $this->connection->table('Mission')->insert($this->missionOne->toArrayForDbEntry());
        
    }
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_show()
    {
        $resuls = [
            'id' => $this->missionOne->id,
            'name' => $this->missionOne->name,
            'description' => $this->missionOne->description,
            'position' => $this->missionOne->position,
            'worksheetForm' => [
                "id" => $this->missionOne->worksheetForm->id,
                "name" => $this->missionOne->worksheetForm->form->name,
                "description" => $this->missionOne->worksheetForm->form->description,
                "stringFields" => [],
                "integerFields" => [],
                "textAreaFields" => [],
                "attachmentFields" => [],
                "singleSelectFields" => [],
                "multiSelectFields" => [],
            ],
            'parent' => [
                'id' => $this->missionOne->parent->id,
                'name' => $this->missionOne->parent->name,
                'description' => $this->missionOne->parent->description,
                'position' => $this->missionOne->parent->position,
            ],
        ];
        $uri = $this->missionUri . "/{$this->missionOne->id}";
        $this->get($uri, $this->participant->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($resuls);
    }
    public function test_show_clientNotActiveParticipant_error401()
    {
        $uri = $this->missionUri . "/{$this->missionOne->id}";
        $this->get($uri, $this->inactiveParticipant->client->token)
                ->seeStatusCode(401);
    }
    
    public function test_showAll()
    {
        $result = [
            'total' => 2, 
            'list' => [
                [
                    'id' => $this->mission->id,
                    'name' => $this->mission->name,
                    'description' => $this->mission->description,
                    'position' => $this->mission->position,
                    'parent' => null,
                ],
                [
                    'id' => $this->missionOne->id,
                    'name' => $this->missionOne->name,
                    'description' => $this->missionOne->description,
                    'position' => $this->missionOne->position,
                    'parent' => [
                        'id' => $this->missionOne->parent->id,
                        'name' => $this->missionOne->parent->name,
                        'description' => $this->missionOne->parent->description,
                        'position' => $this->missionOne->parent->position,
                    ],
                ],
            ],
        ];
        $this->get($this->missionUri, $this->participant->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($result);
    }
    public function test_showAll_containPositionQuery()
    {
        $result = [
            'total' => 1, 
            'list' => [
                [
                    'id' => $this->missionOne->id,
                    'name' => $this->missionOne->name,
                    'description' => $this->missionOne->description,
                    'position' => $this->missionOne->position,
                    'parent' => [
                        'id' => $this->missionOne->parent->id,
                        'name' => $this->missionOne->parent->name,
                        'description' => $this->missionOne->parent->description,
                        'position' => $this->missionOne->parent->position,
                    ],
                ],
            ],
        ];
        $uri = $this->missionUri . "?position={$this->missionOne->position}";
        $this->get($uri, $this->participant->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($result);
    }
    public function test_showAll_clientNotActiveParticipant_error401()
    {
        $this->get($this->missionUri, $this->inactiveParticipant->client->token)
                ->seeStatusCode(401);
    }
}

<?php

namespace Tests\Controllers\Manager\Program;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfMission,
    Firm\RecordOfWorksheetForm,
    Shared\RecordOfForm
};

class MissionControllerTest extends MissionTestCase
{
    protected $missionOne, $publishedMission;
    protected $missionInput, $worksheetFormTwo;
    protected $updateMissionInput = [
        "name" => "new mission name",
        "description" => "new mission description",
        "position" => "new mission position",
    ];


    protected function setUp(): void
    {
        parent::setUp();
        
        $formOne = new RecordOfForm('form-worksheet-form-1');
        $this->connection->table('Form')->insert($formOne->toArrayForDbEntry());
        
        $worksheetFormOne = new RecordOfWorksheetForm($this->firm, $formOne);
        $this->connection->table('WorksheetForm')->insert($worksheetFormOne->toArrayForDbEntry());
        
        $this->missionOne = new RecordOfMission($this->program, $worksheetFormOne, 1, $this->mission);
        $this->publishedMission = new RecordOfMission($this->program, $worksheetFormOne, 2, $this->mission);
        $this->publishedMission->published = true;
        $this->connection->table('Mission')->insert($this->missionOne->toArrayForDbEntry());
        $this->connection->table('Mission')->insert($this->publishedMission->toArrayForDbEntry());
        
        $formTwo = new RecordOfForm('for-worksheet-form-2');
        $this->connection->table('Form')->insert($formTwo->toArrayForDbEntry());
        
        $this->worksheetFormTwo = new RecordOfWorksheetForm($this->firm, $formTwo);
        $this->connection->table('WorksheetForm')->insert($this->worksheetFormTwo->toArrayForDbEntry());
        
        $this->missionInput = [
            "name" => 'new mission name',
            "description" => 'new mission description',
            "position" => 'new mission position',
            "worksheetFormId" => $this->worksheetFormTwo->id,
        ];
    }
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_addRoot()
    {
        $this->connection->table('Mission')->truncate();
        
        $response = [
            "previousMission" => null,
            "name" => $this->missionInput['name'],
            "description" => $this->missionInput['description'],
            "position" => $this->missionInput['position'],
            "published" => false,
            "worksheetForm" => [
                "id" => $this->worksheetFormTwo->id,
                "name" => $this->worksheetFormTwo->form->name,
            ],
        ];
        
        $this->post($this->missionUri, $this->missionInput, $this->manager->token)
            ->seeStatusCode(201)
            ->seeJsonContains($response);
        
        $missionRecord = [
            "Program_id" => $this->program->id,
            "previousMission_id" => null,
            "WorksheetForm_id" => $this->worksheetFormTwo->id,
            "name" => $this->missionInput['name'],
            "description" => $this->missionInput['description'],
            "position" => $this->missionInput['position'],
            "published" => false,
        ];
        $this->seeInDatabase('Mission', $missionRecord);
    }
    public function test_addRoot_emptyName_error400()
    {
        $this->missionInput['name'] = '';
        
        $this->connection->table('Mission')->truncate();
        $this->post($this->missionUri, $this->missionInput, $this->manager->token)
            ->seeStatusCode(400);
    }
    public function test_addRoot_userNotManager_error401()
    {
        $this->connection->table('Mission')->truncate();
        $this->post($this->missionUri, $this->missionInput, $this->removedManager->token)
            ->seeStatusCode(401);
    }
    public function test_addRoot_alreadyContainMission_addNormally()
    {
        $this->post($this->missionUri, $this->missionInput, $this->manager->token)
            ->seeStatusCode(201);
    }
    public function test_addRoot_worksheetNotFound_error404()
    {
        $this->missionInput['worksheetFormId'] = 'non-existing-worksheet';
        $this->post($this->missionUri, $this->missionInput, $this->manager->token)
            ->seeStatusCode(404);
    }
    
    public function test_addBranch()
    {
        $response = [
            "previousMission" => [
                "id" => $this->missionOne->id,
                "name" => $this->missionOne->name,
            ],
            "name" => $this->missionInput['name'],
            "description" => $this->missionInput['description'],
            "position" => $this->missionInput['position'],
            "published" => false,
            "worksheetForm" => [
                "id" => $this->worksheetFormTwo->id,
                "name" => $this->worksheetFormTwo->form->name,
            ],
        ];
        
        $uri = $this->missionUri . "/{$this->missionOne->id}";
        $this->post($uri, $this->missionInput, $this->manager->token)
            ->seeStatusCode(201)
            ->seeJsonContains($response);
        
        $missionRecord = [
            "Program_id" => $this->program->id,
            "previousMission_id" => $this->missionOne->id,
            "WorksheetForm_id" => $this->worksheetFormTwo->id,
            "name" => $this->missionInput['name'],
            "description" => $this->missionInput['description'],
            "position" => $this->missionInput['position'],
            "published" => false,
        ];
        $this->seeInDatabase('Mission', $missionRecord);
    }
    public function test_addBranch_emptyName_error400()
    {
        $this->missionInput['name'] = '';
        $uri = $this->missionUri . "/{$this->missionOne->id}";
        $this->post($uri, $this->missionInput, $this->manager->token)
            ->seeStatusCode(400);
    }
    public function test_addBranch_userNotManager_error401()
    {
        $uri = $this->missionUri . "/{$this->missionOne->id}";
        $this->post($uri, $this->missionInput, $this->removedManager->token)
            ->seeStatusCode(401);
    }
    public function test_addBranch_previousMissionAlreadyHasNextMission_addBranchNormally()
    {
        $uri = $this->missionUri . "/{$this->mission->id}";
        $this->post($uri, $this->missionInput, $this->manager->token)
            ->seeStatusCode(201);
    }
    
    public function test_update()
    {
        $response = [
            "previousMission" => [
                "id" => $this->mission->id,
                "name" => $this->mission->name,
            ],
            "id" => $this->missionOne->id,
            "name" => $this->missionInput['name'],
            "description" => $this->missionInput['description'],
            "position" => $this->missionInput['position'],
            "published" => false,
            "worksheetForm" => [
                "id" => $this->missionOne->worksheetForm->id,
                "name" => $this->missionOne->worksheetForm->form->name,
            ],
        ];
        $uri = $this->missionUri . "/{$this->missionOne->id}/update";
        $this->patch($uri, $this->updateMissionInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $missionRecord = [
            "Program_id" => $this->program->id,
            "id" => $this->missionOne->id,
            "previousMission_id" => $this->mission->id,
            "WorksheetForm_id" => $this->missionOne->worksheetForm->id,
            "name" => $this->missionInput['name'],
            "description" => $this->missionInput['description'],
            "position" => $this->missionInput['position'],
        ];
        $this->seeInDatabase('Mission', $missionRecord);
    }
    public function test_update_userNotManager_error401()
    {
        $uri = $this->missionUri . "/{$this->missionOne->id}/update";
        $this->patch($uri, $this->updateMissionInput, $this->removedManager->token)
            ->seeStatusCode(401);
    }
    public function test_update_missionAlreadyPublished_updateNormally()
    {
        $uri = $this->missionUri . "/{$this->publishedMission->id}/update";
        $this->patch($uri, $this->updateMissionInput, $this->manager->token)
            ->seeStatusCode(200);
    }
    
    public function test_publish()
    {
        $response = [
            "previousMission" => [
                "id" => $this->mission->id,
                "name" => $this->mission->name,
            ],
            "id" => $this->missionOne->id,
            "name" => $this->missionOne->name,
            "description" => $this->missionOne->description,
            "published" => true,
            "worksheetForm" => [
                "id" => $this->missionOne->worksheetForm->id,
                "name" => $this->missionOne->worksheetForm->form->name,
            ],
        ];
        $uri = $this->missionUri . "/{$this->missionOne->id}/publish";
        $this->patch($uri, [], $this->manager->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
        
        $missionEntry = [
            "id" => $this->missionOne->id,
            "published" => true,
        ];
        $this->seeInDatabase('Mission', $missionEntry);
    }
    public function test_publish_userNotManager_error401()
    {
        $uri = $this->missionUri . "/{$this->missionOne->id}/publish";
        $this->patch($uri, [], $this->removedManager->token)
                ->seeStatusCode(401);
    }
    public function test_publish_missionAlreadyPublished_error403()
    {
        $uri = $this->missionUri . "/{$this->publishedMission->id}/publish";
        $this->patch($uri, [], $this->manager->token)
                ->seeStatusCode(403);
    }
    
    public function test_show()
    {
        $response = [
            "previousMission" => [
                "id" => $this->mission->id,
                "name" => $this->mission->name,
            ],
            "id" => $this->missionOne->id,
            "name" => $this->missionOne->name,
            "description" => $this->missionOne->description,
            "position" => $this->missionOne->position,
            "worksheetForm" => [
                "id" => $this->missionOne->worksheetForm->id,
                "name" => $this->missionOne->worksheetForm->form->name,
            ],
        ];
        $uri = $this->missionUri . "/{$this->missionOne->id}";
        $this->get($uri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_show_userNotManager_error401()
    {
        $uri = $this->missionUri . "/{$this->missionOne->id}";
        $this->get($uri, $this->removedManager->token)
            ->seeStatusCode(401);
        
    }
    
    public function test_showAll()
    {
        $response = [
            "total" => 3,
            "list" => [
                [
                    "previousMission" => null,
                    "id" => $this->mission->id,
                    "name" => $this->mission->name,
                    "position" => $this->mission->position,
                    "published" => $this->mission->published,
                ],
                [
                    "previousMission" => [
                        "id" => $this->missionOne->previousMission->id,
                        "name" => $this->missionOne->previousMission->name,
                    ],
                    "id" => $this->missionOne->id,
                    "name" => $this->missionOne->name,
                    "position" => $this->missionOne->position,
                    "published" => $this->missionOne->published,
                ],
                [
                    "previousMission" => [
                        "id" => $this->publishedMission->previousMission->id,
                        "name" => $this->publishedMission->previousMission->name,
                    ],
                    "id" => $this->publishedMission->id,
                    "name" => $this->publishedMission->name,
                    "position" => $this->publishedMission->position,
                    "published" => $this->publishedMission->published,
                ],
            ],
        ];
        $this->get($this->missionUri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
    }
    public function test_showAll_userNotManager_error401()
    {
        $this->get($this->missionUri, $this->removedManager->token)
            ->seeStatusCode(401);
    }
}

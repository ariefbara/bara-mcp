<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\Firm\RecordOfFirmFileInfo;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFileInfo;

class ProgramControllerTest extends ProgramTestCase
{
    protected $firmFileInfoOne;
    protected $programOne;
    protected $programInput;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table("FileInfo")->truncate();
        $this->connection->table("FirmFileInfo")->truncate();
        
        $this->programOne = new RecordOfProgram($this->firm, 1);
        $this->programOne->published = false;
        $this->connection->table('Program')->insert($this->programOne->toArrayForDbEntry());
        
        $fileInfoOne = new RecordOfFileInfo("1");
        $this->firmFileInfoOne = new RecordOfFirmFileInfo($this->firm, $fileInfoOne);
        
        $this->programInput = [
            "name" => "new program name",
            "description" => "new program description",
            "strictMissionOrder" => true,
            "firmFileInfoIdOfIllustration" => $this->firmFileInfoOne->id,
            "participantTypes" => ["client", "user"],
            "programType" => "course",
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("FileInfo")->truncate();
        $this->connection->table("FirmFileInfo")->truncate();
    }
    
    protected function add()
    {
        $this->firmFileInfoOne->insert($this->connection);
        $this->post($this->programUri, $this->programInput, $this->manager->token);
    }
    public function test_add()
    {
        $this->add();
        $this->seeStatusCode(201);
        
        $response = [
            "name" => $this->programInput['name'],
            "description" => $this->programInput['description'],
            "participantTypes" => $this->programInput['participantTypes'],
            "programType" => $this->programInput['programType'],
            "strictMissionOrder" => $this->programInput['strictMissionOrder'],
            "published" => false,
            "illustration" => [
                "id" => $this->firmFileInfoOne->id,
                "url" => "/{$this->firmFileInfoOne->fileInfo->name}"
            ],
        ];
        $this->seeJsonContains($response);
        
        $programRecord = [
            "Firm_id" => $this->firm->id,
            "name" => $this->programInput['name'],
            "description" => $this->programInput['description'],
            "strictMissionOrder" => $this->programInput['strictMissionOrder'],
            "participantTypes" => "client,user",
            "programType" => $this->programInput['programType'],
            "FirmFileInfo_idOfIllustration" => $this->firmFileInfoOne->id,
            "published" => false,
            "removed" => false,
        ];
        $this->seeInDatabase('Program', $programRecord);
    }
    public function test_add_userNotManager_error401()
    {
        $this->firmFileInfoOne->insert($this->connection);
        $this->post($this->programUri, $this->programInput, $this->removedManager->token)
            ->seeStatusCode(401);
    }
    
    protected function update()
    {
        $this->firmFileInfoOne->insert($this->connection);
        $uri = "{$this->programUri}/{$this->program->id}/update";
        $this->patch($uri, $this->programInput, $this->manager->token);
    }
    public function test_update()
    {
        $this->update();
        $this->seeStatusCode(200);
        $response = [
            "id" => $this->program->id,
            "name" => $this->programInput['name'],
            "description" => $this->programInput['description'],
            "participantTypes" => $this->programInput['participantTypes'],
            "programType" => $this->programInput['programType'],
            "published" => $this->program->published,
            "illustration" => [
                "id" => $this->firmFileInfoOne->id,
                "url" => "/{$this->firmFileInfoOne->fileInfo->name}"
            ],
        ];
        
        $programmeRecord = [
            "id" => $this->program->id,
            "name" => $this->programInput['name'],
            "description" => $this->programInput['description'],
            "participantTypes" => "client,user",
            "programType" => $this->programInput['programType'],
            "published" => $this->program->published,
            "removed" => false,
            "FirmFileInfo_idOfIllustration" => $this->firmFileInfoOne->id,
        ];
        $this->seeInDatabase('Program', $programmeRecord);
    }
    public function test_update_userNotManager_error401()
    {
        $this->firmFileInfoOne->insert($this->connection);
        $uri = "{$this->programUri}/{$this->program->id}/update";
        $this->patch($uri, $this->programInput, $this->removedManager->token)
            ->seeStatusCode(401);
    }
    
    public function test_publish()
    {
        $response = [
            "id" => $this->program->id,
            "published" => true,
        ];
        
        $uri = "{$this->programUri}/{$this->program->id}/publish";
        $this->patch($uri, [], $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $programmeRecord = [
            "id" => $this->program->id,
            "published" => true,
            "removed" => false,
        ];
        $this->seeInDatabase('Program', $programmeRecord);
        
    }
    public function test_publish_userNotManager_error401()
    {
        $uri = "{$this->programUri}/{$this->program->id}/publish";
        $this->patch($uri, [], $this->removedManager->token)
            ->seeStatusCode(401);
    }
    
    public function test_remove()
    {
        $uri = "{$this->programUri}/{$this->programOne->id}";
        $this->delete($uri, [], $this->manager->token)
            ->seeStatusCode(200);
        
        $programmeRecord = [
            "id" => $this->programOne->id,
            "removed" => true,
        ];
        $this->seeInDatabase('Program', $programmeRecord);
    }
    public function test_remove_userNotManager_error403()
    {
        $uri = "{$this->programUri}/{$this->programOne->id}";
        $this->delete($uri, [], $this->removedManager->token)
            ->seeStatusCode(401);
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->program->id,
            "name" => $this->program->name,
            "description" => $this->program->description,
            "strictMissionOrder" => $this->program->strictMissionOrder,
            "programType" => $this->program->programType,
            "published" => $this->program->published,
            "illustration" => null,
        ];
        $uri = "{$this->programUri}/{$this->program->id}";
        $this->get($uri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_show_usetNotManager_error401()
    {
        $uri = "{$this->programUri}/{$this->program->id}";
        $this->get($uri, $this->removedManager->token)
            ->seeStatusCode(401);
    }
    
    public function test_showAll()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->program->id,
                    "name" => $this->program->name,
                    "published" => $this->program->published,
                    "participantTypes" => explode(',', $this->program->participantTypes),
                    "programType" => $this->program->programType,
                ],
                [
                    "id" => $this->programOne->id,
                    "name" => $this->programOne->name,
                    "published" => $this->programOne->published,
                    "participantTypes" => explode(',', $this->programOne->participantTypes),
                    "programType" => $this->programOne->programType,
                ],
            ],
        ];
        $this->get($this->programUri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_showAll_userNotManager_error401()
    {
        $this->get($this->programUri, $this->removedManager->token)
            ->seeStatusCode(401);
    }
}

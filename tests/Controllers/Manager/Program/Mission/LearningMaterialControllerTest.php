<?php

namespace Tests\Controllers\Manager\Program\Mission;

use Tests\Controllers\Manager\Program\MissionTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\Mission\LearningMaterial\RecordOfLearningAttachment;
use Tests\Controllers\RecordPreparation\Firm\Program\Mission\RecordOfLearningMaterial;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFirmFileInfo;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFileInfo;

class LearningMaterialControllerTest extends MissionTestCase
{

    protected $learningMaterialUri;
    protected $learningMaterial, $learningMaterialOne;
    protected $learningAttachmentOne;
    protected $learningMaterialInput;

    protected function setUp(): void
    {
        parent::setUp();
        $this->learningMaterialUri = $this->missionUri . "/{$this->mission->id}/learning-materials";
        $this->connection->table('LearningMaterial')->truncate();$this->connection->table('LearningAttachment')->truncate();
        $this->connection->table('FirmFileInfo')->truncate();
        $this->connection->table('FileInfo')->truncate();

        $this->learningMaterial = new RecordOfLearningMaterial($this->mission, 0);
        $this->learningMaterialOne = new RecordOfLearningMaterial($this->mission, 1);
        $this->connection->table('LearningMaterial')->insert($this->learningMaterial->toArrayForDbEntry());
        $this->connection->table('LearningMaterial')->insert($this->learningMaterialOne->toArrayForDbEntry());
        
        $fileInfoOne = new RecordOfFileInfo(1, 'firm-main-identifier', 'video.mp4', '/video/mp4');
        $firmFileInfoOne = new RecordOfFirmFileInfo($this->firm, $fileInfoOne);
        $this->learningAttachmentOne = new RecordOfLearningAttachment($this->learningMaterial, $firmFileInfoOne, 1);
        
        $this->learningAttachmentOne->firmFileInfo->insert($this->connection);
        $this->learningAttachmentOne->insert($this->connection);

        $this->learningMaterialInput = [
            "name" => 'new learning material name',
            "content" => 'new learning material content',
        ];
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('LearningMaterial')->truncate();
        $this->connection->table('LearningAttachment')->truncate();
        $this->connection->table('FirmFileInfo')->truncate();
        $this->connection->table('FileInfo')->truncate();
    }

    public function test_add()
    {
$this->disableExceptionHandling();
        $response = [
            "name" => $this->learningMaterialInput['name'],
            "content" => $this->learningMaterialInput['content'],
        ];
        $this->post($this->learningMaterialUri, $this->learningMaterialInput, $this->manager->token)
                ->seeStatusCode(201)
                ->seeJsonContains($response);

        $learningMaterialEntry = [
            "Mission_id" => $this->mission->id,
            "name" => $this->learningMaterialInput['name'],
            "content" => $this->learningMaterialInput['content'],
            "removed" => false,
        ];
        $this->seeInDatabase('LearningMaterial', $learningMaterialEntry);
//$this->printApiSpesifiation($this->learningMaterialUri, $this->learningMaterialInput);
    }
    public function test_add_userNotManager_error401()
    {
        $this->post($this->learningMaterialUri, $this->learningMaterialInput, $this->removedManager->token)
                ->seeStatusCode(401);
    }

    public function test_update()
    {
$this->disableExceptionHandling();
        $response = [
            "id" => $this->learningMaterial->id,
            "name" => $this->learningMaterialInput['name'],
            "content" => $this->learningMaterialInput['content'],
        ];

        $uri = $this->learningMaterialUri . "/{$this->learningMaterial->id}";
        $this->patch($uri, $this->learningMaterialInput, $this->manager->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);

        $learningMaterialEntry = [
            "id" => $this->learningMaterial->id,
            "Mission_id" => $this->mission->id,
            "name" => $this->learningMaterialInput['name'],
            "content" => $this->learningMaterialInput['content'],
            "removed" => false,
        ];
        $this->seeInDatabase('LearningMaterial', $learningMaterialEntry);
//$this->printApiSpesifiation($uri, $this->learningMaterialInput);
    }
    public function test_update_userNotManager_error401()
    {
        $uri = $this->learningMaterialUri . "/{$this->learningMaterial->id}";
        $this->patch($uri, $this->learningMaterialInput, $this->removedManager->token)
                ->seeStatusCode(401);
    }

    public function test_remove()
    {
        $uri = $this->learningMaterialUri . "/{$this->learningMaterial->id}";
        $this->delete($uri, [], $this->manager->token)
                ->seeStatusCode(200);

        $learningMaterialEntry = [
            "id" => $this->learningMaterial->id,
            "Mission_id" => $this->mission->id,
            "removed" => true,
        ];
        $this->seeInDatabase('LearningMaterial', $learningMaterialEntry);
//$this->printApiSpesifiation($uri, []);
    }
    public function test_remove_userNotManager_error401()
    {
        $uri = $this->learningMaterialUri . "/{$this->learningMaterial->id}";
        $this->delete($uri, [], $this->removedManager->token)
                ->seeStatusCode(401);
    }

    public function test_show()
    {
        $response = [
            "id" => $this->learningMaterial->id,
            "name" => $this->learningMaterial->name,
            "content" => $this->learningMaterial->content,
        ];
        $uri = $this->learningMaterialUri . "/{$this->learningMaterial->id}";
        $this->get($uri, $this->manager->token);
        $this->seeStatusCode(200);
        $this->seeJsonContains([
            "id" => $this->learningMaterial->id,
            "name" => $this->learningMaterial->name,
            "content" => $this->learningMaterial->content,
        ]);
//$this->printApiSpesifiation($uri, []);
    }
    public function test_show_userNotManager_error401()
    {
        $uri = $this->learningMaterialUri . "/{$this->learningMaterial->id}";
        $this->get($uri, $this->removedManager->token)
                ->seeStatusCode(401);
    }

    public function test_showAll()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->learningMaterial->id,
                    "name" => $this->learningMaterial->name,
                ],
                [
                    "id" => $this->learningMaterialOne->id,
                    "name" => $this->learningMaterialOne->name,
                ],
            ],
        ];
        $this->get($this->learningMaterialUri, $this->manager->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
//$this->printApiSpesifiation($this->learningMaterialUri, []);
    }
    public function test_showAll_userNotManager_error401()
    {
        $this->get($this->learningMaterialUri, $this->removedManager->token)
                ->seeStatusCode(401);
    }

}

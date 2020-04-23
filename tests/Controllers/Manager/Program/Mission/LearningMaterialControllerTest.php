<?php

namespace Tests\Controllers\Manager\Program\Mission;

use Tests\Controllers\ {
    Manager\Program\MissionTestCase,
    RecordPreparation\Firm\Program\Mission\RecordOfLearningMaterial
};

class LearningMaterialControllerTest extends MissionTestCase
{

    protected $learningMaterialUri;
    protected $learningMaterial, $learningMaterialOne;
    protected $learningMaterialInput;

    protected function setUp(): void
    {
        parent::setUp();
        $this->learningMaterialUri = $this->missionUri . "/{$this->mission->id}/learning-materials";
        $this->connection->table('LearningMaterial')->truncate();

        $this->learningMaterial = new RecordOfLearningMaterial($this->mission, 0);
        $this->learningMaterialOne = new RecordOfLearningMaterial($this->mission, 1);
        $this->connection->table('LearningMaterial')->insert($this->learningMaterial->toArrayForDbEntry());
        $this->connection->table('LearningMaterial')->insert($this->learningMaterialOne->toArrayForDbEntry());

        $this->learningMaterialInput = [
            "name" => 'new learning material name',
            "content" => 'new learning material content',
        ];
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('LearningMaterial')->truncate();
    }

    public function test_add()
    {
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
        $this->get($uri, $this->manager->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
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
    }

    public function test_showAll_userNotManager_error401()
    {
        $this->get($this->learningMaterialUri, $this->removedManager->token)
                ->seeStatusCode(401);
    }

}

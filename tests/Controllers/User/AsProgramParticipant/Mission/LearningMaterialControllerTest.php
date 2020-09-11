<?php

namespace Tests\Controllers\User\AsProgramParticipant\Mission;

use Tests\Controllers\ {
    User\AsProgramParticipant\MissionTestCase,
    RecordPreparation\Firm\Program\Mission\RecordOfLearningMaterial
};

class LearningMaterialControllerTest extends MissionTestCase
{
    protected $learningMaterialUri;
    protected $learningMaterial;
    protected $learningMaterialOne;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->learningMaterialUri = $this->missionUri . "/{$this->mission->id}/learning-materials";
        $this->connection->table('LearningMaterial')->truncate();
        
        $this->learningMaterial = new RecordOfLearningMaterial($this->mission, 0);
        $this->learningMaterialOne = new RecordOfLearningMaterial($this->mission, 1);
        $this->connection->table('LearningMaterial')->insert($this->learningMaterial->toArrayForDbEntry());
        $this->connection->table('LearningMaterial')->insert($this->learningMaterialOne->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('LearningMaterial')->truncate();
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->learningMaterial->id,
            "name" => $this->learningMaterial->name,
            "content" => $this->learningMaterial->content,
        ];
        $uri = $this->learningMaterialUri . "/{$this->learningMaterial->id}";
        $this->get($uri, $this->programParticipation->user->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_show_userNotActiveParticipant_error403()
    {
        $uri = $this->learningMaterialUri . "/{$this->learningMaterial->id}";
        $this->get($uri, $this->inactiveProgramParticipation->user->token)
                ->seeStatusCode(403);
    }
    public function test_showAll()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->learningMaterial->id,
                    "name" => $this->learningMaterial->name,
                    "content" => $this->learningMaterial->content,
                ],
                [
                    "id" => $this->learningMaterialOne->id,
                    "name" => $this->learningMaterialOne->name,
                    "content" => $this->learningMaterialOne->content,
                ],
            ],
        ];
        $this->get($this->learningMaterialUri, $this->programParticipation->user->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_showAll_userNotActiveParticipant()
    {
        $this->get($this->learningMaterialUri, $this->inactiveProgramParticipation->user->token)
                ->seeStatusCode(403);
    }
}

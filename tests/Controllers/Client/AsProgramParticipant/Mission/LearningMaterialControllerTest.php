<?php

namespace Tests\Controllers\Client\AsProgramParticipant\Mission;

use DateTimeImmutable;
use Tests\Controllers\ {
    Client\AsProgramParticipant\MissionTestCase,
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
        
        $this->connection->table("LearningMaterial")->truncate();
        $this->connection->table("ActivityLog")->truncate();
        $this->connection->table("ViewLearningMaterialActivityLog")->truncate();
        
        $this->learningMaterial = new RecordOfLearningMaterial($this->mission, 0);
        $this->learningMaterialOne = new RecordOfLearningMaterial($this->mission, 1);
        $this->connection->table("LearningMaterial")->insert($this->learningMaterial->toArrayForDbEntry());
        $this->connection->table("LearningMaterial")->insert($this->learningMaterialOne->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("LearningMaterial")->truncate();
        $this->connection->table("ActivityLog")->truncate();
        $this->connection->table("ViewLearningMaterialActivityLog")->truncate();
    }
    
    public function test_showAll_200()
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
        
        $this->get($this->learningMaterialUri, $this->programParticipation->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_inactiveParticipant_403()
    {
        $this->setInactiveParticipant();
        $this->get($this->learningMaterialUri, $this->programParticipation->client->token)
                ->seeStatusCode(403);
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->learningMaterial->id,
            "name" => $this->learningMaterial->name,
            "content" => $this->learningMaterial->content,
        ];
        $uri = $this->learningMaterialUri . "/{$this->learningMaterial->id}";
        $this->get($uri, $this->programParticipation->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_show_inactiveParticipant_403()
    {
        $this->setInactiveParticipant();
        $uri = $this->learningMaterialUri . "/{$this->learningMaterial->id}";
        $this->get($uri, $this->programParticipation->client->token)
                ->seeStatusCode(403);
    }
    public function test_show_logActivity()
    {
        $uri = $this->learningMaterialUri . "/{$this->learningMaterial->id}";
        $this->get($uri, $this->programParticipation->client->token)
                ->seeStatusCode(200);
        
        $activityLogEntry = [
            "message" => "accessed learning material",
            "occuredTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
        ];
        $this->seeInDatabase("ActivityLog", $activityLogEntry);
        
        $viewLearningMaterialActivityLogEntry = [
            "Participant_id" => $this->programParticipation->participant->id,
            "LearningMaterial_id" => $this->learningMaterial->id,
        ];
        $this->seeInDatabase("ViewLearningMaterialActivityLog", $viewLearningMaterialActivityLogEntry);
    }
}

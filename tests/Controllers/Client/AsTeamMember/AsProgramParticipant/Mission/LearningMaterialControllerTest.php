<?php

namespace Tests\Controllers\Client\AsTeamMember\AsProgramParticipant\Mission;

use Tests\Controllers\ {
    Client\AsTeamMember\AsProgramParticipant\MissionTestCase,
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
        $this->connection->table("TeamMemberActivityLog")->truncate();
        
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
        $this->connection->table("TeamMemberActivityLog")->truncate();
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
        
        $this->get($this->learningMaterialUri, $this->teamMember->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_inactiveMember_403()
    {
        $this->get($this->learningMaterialUri, $this->teamMemberOne_inactive->client->token)
                ->seeStatusCode(403);
    }
    public function test_showAll_inactiveParticipant_403()
    {
        $this->setInactiveParticipant();
        $this->get($this->learningMaterialUri, $this->teamMember->client->token)
                ->seeStatusCode(403);
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->learningMaterial->id,
            "name" => $this->learningMaterial->name,
            "content" => $this->learningMaterial->content,
            "removed" => $this->learningMaterial->removed,
        ];
        $uri = $this->learningMaterialUri . "/{$this->learningMaterial->id}";
        $this->get($uri, $this->teamMember->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_show_inactiveMember_403()
    {
        $uri = $this->learningMaterialUri . "/{$this->learningMaterial->id}";
        $this->get($uri, $this->teamMemberOne_inactive->client->token)
                ->seeStatusCode(403);
    }
    public function test_show_inactiveParticipant_403()
    {
        $this->setInactiveParticipant();
        $uri = $this->learningMaterialUri . "/{$this->learningMaterial->id}";
        $this->get($uri, $this->teamMember->client->token)
                ->seeStatusCode(403);
    }
    public function test_show_logActivity()
    {
        $uri = $this->learningMaterialUri . "/{$this->learningMaterial->id}";
        $this->get($uri, $this->teamMember->client->token)
                ->seeStatusCode(200);
        
        $activityLogEntry = [
            "message" => "accessed learning material",
            "occuredTime" => (new \DateTimeImmutable())->format("Y-m-d H:i:s"),
        ];
        $this->seeInDatabase("ActivityLog", $activityLogEntry);
        
        $viewLearningMaterialActivityLogEntry = [
            "Participant_id" => $this->programParticipant->participant->id,
            "LearningMaterial_id" => $this->learningMaterial->id,
        ];
        $this->seeInDatabase("ViewLearningMaterialActivityLog", $viewLearningMaterialActivityLogEntry);
        
        $teamMemberAcivityLogEntry = [
            "TeamMember_id" => $this->teamMember->id,
        ];
        $this->seeInDatabase("TeamMemberActivityLog", $teamMemberAcivityLogEntry);
    }
}

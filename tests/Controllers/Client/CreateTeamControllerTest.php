<?php

namespace Tests\Controllers\Client;

use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;

class CreateTeamControllerTest extends ClientTestCase
{
    protected $createTeamUri;
    protected $team;
    protected $createInput = [
        "name" => "new team name",
        "memberPosition" => "new member position",
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTeamUri = $this->clientUri . "/create-team";
        
        $this->connection->table("Team")->truncate();
        $this->connection->table("T_Member")->truncate();
        
        $firm = $this->client->firm;
        $this->team = new RecordOfTeam($firm, $this->client, 0);
        $this->connection->table("Team")->insert($this->team->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Team")->truncate();
        $this->connection->table("T_Member")->truncate();
    }
    
    public function test_create_201()
    {
        $response = [
            "name" => $this->createInput["name"],
            "createdTime" => (new \DateTimeImmutable())->format("Y-m-d H:i:s"),
            "creator" => [
                "id" => $this->client->id,
                "name" => $this->client->getFullName(),
            ],
        ];
        $this->post($this->createTeamUri, $this->createInput, $this->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(201);
        
        $teamEntry = [
            "name" => $this->createInput["name"],
            "createdTime" => (new \DateTimeImmutable())->format("Y-m-d H:i:s"),
            "Client_idOfCreator" => $this->client->id,
        ];
        $this->seeInDatabase("Team", $teamEntry);
        
    }
    public function test_create_aggreageMember()
    {
        $this->post($this->createTeamUri, $this->createInput, $this->client->token)
                ->seeStatusCode(201);
        
        $memberEntry = [
            "Client_id" => $this->client->id,
            "position" => $this->createInput["memberPosition"],
            "anAdmin" => true,
            "active" => true,
            "joinTime" => (new \DateTimeImmutable())->format("Y-m-d H:i:s"),
        ];
        $this->seeInDatabase("T_Member", $memberEntry);
    }
    public function test_create_emptyName_400()
    {
        $this->createInput["name"] = "";
        $this->post($this->createTeamUri, $this->createInput, $this->client->token)
                ->seeStatusCode(400);
    }
    public function test_create_duplicatName_409()
    {
        $this->createInput["name"] = $this->team->name;
        $this->post($this->createTeamUri, $this->createInput, $this->client->token)
                ->seeStatusCode(409);
    }
    public function test_create_inactiveClient_403()
    {
        $this->post($this->createTeamUri, $this->createInput, $this->inactiveClient->token)
                ->seeStatusCode(403);
    }
}

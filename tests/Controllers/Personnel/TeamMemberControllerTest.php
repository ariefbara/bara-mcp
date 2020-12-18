<?php

namespace Tests\Controllers\Personnel;

use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfMember;

class TeamMemberControllerTest extends PersonnelTestCase
{
    protected $team;
    protected $teamMemberOne;
    protected $teamMemberTwo_inactive;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table("Client")->truncate();
        $this->connection->table("Team")->truncate();
        $this->connection->table("T_Member")->truncate();
        
        $firm = $this->personnel->firm;
        
        $clientOne = new RecordOfClient($firm, 1);
        $clientTwo = new RecordOfClient($firm, 2);
        $this->connection->table("Client")->insert($clientOne->toArrayForDbEntry());
        $this->connection->table("Client")->insert($clientTwo->toArrayForDbEntry());
        
        $this->team = new RecordOfTeam($firm, $clientOne, 999);
        $this->connection->table("Team")->insert($this->team->toArrayForDbEntry());
        
        $this->teamMemberOne = new RecordOfMember($this->team, $clientOne, 1);
        $this->teamMemberTwo_inactive = new RecordOfMember($this->team, $clientTwo, 2);
        $this->teamMemberTwo_inactive->active = false;
        $this->connection->table("T_Member")->insert($this->teamMemberOne->toArrayForDbEntry());
        $this->connection->table("T_Member")->insert($this->teamMemberTwo_inactive->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Client")->truncate();
        $this->connection->table("Team")->truncate();
        $this->connection->table("T_Member")->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->teamMemberOne->id,
            "active" => $this->teamMemberOne->active,
            "anAdmin" => $this->teamMemberOne->anAdmin,
            "position" => $this->teamMemberOne->position,
            "joinTime" => $this->teamMemberOne->joinTime,
            "client" => [
                "id" => $this->teamMemberOne->client->id,
                "name" => $this->teamMemberOne->client->getFullName(),
            ],
        ];
        
        $uri = $this->personnelUri . "/team-members/{$this->teamMemberOne->id}";
        $this->get($uri, $this->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    
    public function test_showAll_200()
    {
        $totalResponse = ["total" => 2];
        $listResponse = [
            "id" => $this->teamMemberOne->id,
            "id" => $this->teamMemberTwo_inactive->id,
        ];
        $detailResponse = [
            "id" => $this->teamMemberOne->id,
            "active" => $this->teamMemberOne->active,
            "anAdmin" => $this->teamMemberOne->anAdmin,
            "position" => $this->teamMemberOne->position,
            "joinTime" => $this->teamMemberOne->joinTime,
            "client" => [
                "id" => $this->teamMemberOne->client->id,
                "name" => $this->teamMemberOne->client->getFullName(),
            ],
        ];
        
        $uri = $this->personnelUri . "/team/{$this->team->id}/members";
        $this->get($uri, $this->personnel->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($listResponse)
                ->seeJsonContains($detailResponse)
                ->seeStatusCode(200);
    }
    public function test_showAll_activeStatusFilterSet_200()
    {
        $totalResponse = ["total" => 1];
        $listResponse = [
            "id" => $this->teamMemberOne->id,
        ];
        $uri = $this->personnelUri . "/team/{$this->team->id}/members?activeStatus=true";
        $this->get($uri, $this->personnel->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($listResponse)
                ->seeStatusCode(200);
    }
    
}

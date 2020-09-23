<?php

namespace Tests\Controllers\Client;

use Tests\Controllers\RecordPreparation\Firm\ {
    RecordOfTeam,
    Team\RecordOfMember
};

class TeamMembershipControllerTest extends ClientTestCase
{
    protected $teamMembershipUri;
    protected $teamMembership;
    protected $teamMembershipOne_inactive;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->teamMembershipUri = $this->clientUri . "/team-memberships";
        
        $this->connection->table("Team")->truncate();
        $this->connection->table("T_Member")->truncate();
        
        $firm = $this->client->firm;
        
        $team = new RecordOfTeam($firm, $this->client, 0);
        $teamOne = new RecordOfTeam($firm, $this->client, 1);
        $this->connection->table("Team")->insert($team->toArrayForDbEntry());
        $this->connection->table("Team")->insert($teamOne->toArrayForDbEntry());
        
        $this->teamMembership = new RecordOfMember($team, $this->client, 0);
        $this->teamMembershipOne_inactive = new RecordOfMember($teamOne, $this->client, 1);
        $this->teamMembershipOne_inactive->active = false;
        $this->connection->table("T_Member")->insert($this->teamMembership->toArrayForDbEntry());
        $this->connection->table("T_Member")->insert($this->teamMembershipOne_inactive->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Team")->truncate();
        $this->connection->table("T_Member")->truncate();
    }
    
    public function test_quit_200()
    {
        $uri = $this->teamMembershipUri . "/{$this->teamMembership->id}";
        $this->delete($uri, [], $this->client->token)
                ->seeStatusCode(200);
        
        $memberEntry = [
            "id" => $this->teamMembership->id,
            "active" => false,
        ];
        $this->seeInDatabase("T_Member", $memberEntry);
    }
    public function test_quit_alreadyInactive_403()
    {
        $uri = $this->teamMembershipUri . "/{$this->teamMembershipOne_inactive->id}";
        $this->delete($uri, [], $this->client->token)
                ->seeStatusCode(403);
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->teamMembership->id,
            "position" => $this->teamMembership->position,
            "anAdmin" => $this->teamMembership->anAdmin,
            "active" => $this->teamMembership->active,
            "joinTime" => $this->teamMembership->joinTime,
            "team" => [
                "id" => $this->teamMembership->team->id,
                "name" => $this->teamMembership->team->name,
            ],
        ];
        $uri = $this->teamMembershipUri . "/{$this->teamMembership->id}";
        $this->get($uri, $this->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->teamMembership->id,
                    "position" => $this->teamMembership->position,
                    "anAdmin" => $this->teamMembership->anAdmin,
                    "active" => $this->teamMembership->active,
                    "joinTime" => $this->teamMembership->joinTime,
                    "team" => [
                        "id" => $this->teamMembership->team->id,
                        "name" => $this->teamMembership->team->name,
                    ],
                ],
                [
                    "id" => $this->teamMembershipOne_inactive->id,
                    "position" => $this->teamMembershipOne_inactive->position,
                    "anAdmin" => $this->teamMembershipOne_inactive->anAdmin,
                    "active" => $this->teamMembershipOne_inactive->active,
                    "joinTime" => $this->teamMembershipOne_inactive->joinTime,
                    "team" => [
                        "id" => $this->teamMembershipOne_inactive->team->id,
                        "name" => $this->teamMembershipOne_inactive->team->name,
                    ],
                ],
            ],
        ];
        $this->get($this->teamMembershipUri, $this->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}

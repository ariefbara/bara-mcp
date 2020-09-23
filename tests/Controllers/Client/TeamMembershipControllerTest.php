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
    protected $teamMembershipOne;
    
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
        $this->teamMembershipOne = new RecordOfMember($teamOne, $this->client, 1);
        $this->connection->table("T_Member")->insert($this->teamMembership->toArrayForDbEntry());
        $this->connection->table("T_Member")->insert($this->teamMembershipOne->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Team")->truncate();
        $this->connection->table("T_Member")->truncate();
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
                    "id" => $this->teamMembershipOne->id,
                    "position" => $this->teamMembershipOne->position,
                    "anAdmin" => $this->teamMembershipOne->anAdmin,
                    "active" => $this->teamMembershipOne->active,
                    "joinTime" => $this->teamMembershipOne->joinTime,
                    "team" => [
                        "id" => $this->teamMembershipOne->team->id,
                        "name" => $this->teamMembershipOne->team->name,
                    ],
                ],
            ],
        ];
        $this->get($this->teamMembershipUri, $this->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}

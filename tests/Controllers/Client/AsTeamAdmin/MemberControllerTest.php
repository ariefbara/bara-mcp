<?php

namespace Tests\Controllers\Client\AsTeamAdmin;

use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\Firm\ {
    RecordOfClient,
    Team\RecordOfMember
};

class MemberControllerTest extends AsTeamAdminTestCase
{
    protected $memberUri;
    protected $member;
    protected $memberOne_inactive;
    protected $client;
    
    protected $addMemberInput;

    protected function setUp(): void
    {
        parent::setUp();
        $this->memberUri = $this->asTeamAdminUri . "/members";
        
        $team = $this->memberWithAdminPriviledge->team;
        $firm = $team->firm;
        
        $client = new RecordOfClient($firm, 0);
        $clientOne = new RecordOfClient($firm, 1);
        $this->client = new RecordOfClient($firm, "newClient");
        $this->connection->table("Client")->insert($client->toArrayForDbEntry());
        $this->connection->table("Client")->insert($clientOne->toArrayForDbEntry());
        $this->connection->table("Client")->insert($this->client->toArrayForDbEntry());
        
        $this->member = new RecordOfMember($team, $client, 0);
        $this->memberOne_inactive = new RecordOfMember($team, $clientOne, 1);
        $this->memberOne_inactive->active = false;
        $this->connection->table("T_Member")->insert($this->member->toArrayForDbEntry());
        $this->connection->table("T_Member")->insert($this->memberOne_inactive->toArrayForDbEntry());
        
        $this->addMemberInput = [
            "clientId" => $this->client->id,
            "anAdmin" => false,
            "memberPosition" => "programmer",
        ];
    }
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_add_200()
    {
        $response = [
            "position" => $this->addMemberInput["memberPosition"],
            "anAdmin" => $this->addMemberInput["anAdmin"],
            "active" => true,
            "joinTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
            "client" => [
                "id" => $this->client->id,
                "name" => $this->client->getFullName(),
            ],
        ];
        
        $this->post($this->memberUri, $this->addMemberInput, $this->memberWithAdminPriviledge->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
        
        $memberEntry = [
            "position" => $this->addMemberInput["memberPosition"],
            "anAdmin" => $this->addMemberInput["anAdmin"],
            "active" => true,
            "joinTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
            "Client_id" => $this->client->id,
        ];
        $this->seeInDatabase("T_Member", $memberEntry);
    }
    public function test_add_clientIsInactiveMember_activateInactiveMember()
    {
        $this->addMemberInput["clientId"] = $this->memberOne_inactive->client->id;
        $response = [
            "id" => $this->memberOne_inactive->id,
            "position" => $this->addMemberInput["memberPosition"],
            "anAdmin" => $this->addMemberInput["anAdmin"],
            "active" => true,
            "joinTime" => $this->memberOne_inactive->joinTime,
            "client" => [
                "id" => $this->memberOne_inactive->client->id,
                "name" => $this->memberOne_inactive->client->getFullName(),
            ],
        ];
        
        $this->post($this->memberUri, $this->addMemberInput, $this->memberWithAdminPriviledge->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
        
        $memberEntry = [
            "id" => $this->memberOne_inactive->id,
            "position" => $this->addMemberInput["memberPosition"],
            "anAdmin" => $this->addMemberInput["anAdmin"],
            "active" => true,
            "joinTime" => $this->memberOne_inactive->joinTime,
            "Client_id" => $this->memberOne_inactive->client->id,
        ];
        $this->seeInDatabase("T_Member", $memberEntry);
    }
    public function test_add_clientAlreadyActiveMember_403()
    {
        $this->addMemberInput["clientId"] = $this->member->client->id;
        $this->post($this->memberUri, $this->addMemberInput, $this->memberWithAdminPriviledge->client->token)
                ->seeStatusCode(403);
    }
    public function test_add_requestFromNonAdminMember_403()
    {
        $this->post($this->memberUri, $this->addMemberInput, $this->memberWithoutAdminPriviledge->client->token)
                ->seeStatusCode(403);
    }
    
    public function test_remove_200()
    {
        $uri = $this->memberUri . "/{$this->member->id}";
        $this->delete($uri, [], $this->memberWithAdminPriviledge->client->token)
                ->seeStatusCode(200);
        
        $memberEntry = [
            "id" => $this->member->id,
            "active" => false,
        ];
        $this->seeInDatabase("T_Member", $memberEntry);
    }
    public function test_remove_alreadyInactiveMember_403()
    {
        $uri = $this->memberUri . "/{$this->memberOne_inactive->id}";
        $this->delete($uri, [], $this->memberWithAdminPriviledge->client->token)
                ->seeStatusCode(403);
    }
    public function test_remove_requestFromNonAdminMember_403()
    {
        $uri = $this->memberUri . "/{$this->member->id}";
        $this->delete($uri, [], $this->memberWithoutAdminPriviledge->client->token)
                ->seeStatusCode(403);
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->member->id,
            "position" => $this->member->position,
            "anAdmin" => $this->member->anAdmin,
            "active" => $this->member->active,
            "joinTime" => $this->member->joinTime,
            "client" => [
                "id" => $this->member->client->id,
                "name" => $this->member->client->getFullName(),
            ],
        ];
        $uri = $this->memberUri . "/{$this->member->id}";
        $this->get($uri, $this->memberWithAdminPriviledge->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_show_requestFromNonAdminMember_403()
    {
        $uri = $this->memberUri . "/{$this->member->id}";
        $this->get($uri, $this->memberWithoutAdminPriviledge->client->token)
                ->seeStatusCode(403);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 4,
            "list" => [
                [
                    "id" => $this->member->id,
                    "position" => $this->member->position,
                    "anAdmin" => $this->member->anAdmin,
                    "active" => $this->member->active,
                    "joinTime" => $this->member->joinTime,
                    "client" => [
                        "id" => $this->member->client->id,
                        "name" => $this->member->client->getFullName(),
                    ],
                ],
                [
                    "id" => $this->memberOne_inactive->id,
                    "position" => $this->memberOne_inactive->position,
                    "anAdmin" => $this->memberOne_inactive->anAdmin,
                    "active" => $this->memberOne_inactive->active,
                    "joinTime" => $this->memberOne_inactive->joinTime,
                    "client" => [
                        "id" => $this->memberOne_inactive->client->id,
                        "name" => $this->memberOne_inactive->client->getFullName(),
                    ],
                ],
                [
                    "id" => $this->memberWithAdminPriviledge->id,
                    "position" => $this->memberWithAdminPriviledge->position,
                    "anAdmin" => $this->memberWithAdminPriviledge->anAdmin,
                    "active" => $this->memberWithAdminPriviledge->active,
                    "joinTime" => $this->memberWithAdminPriviledge->joinTime,
                    "client" => [
                        "id" => $this->memberWithAdminPriviledge->client->id,
                        "name" => $this->memberWithAdminPriviledge->client->getFullName(),
                    ],
                ],
                [
                    "id" => $this->memberWithoutAdminPriviledge->id,
                    "position" => $this->memberWithoutAdminPriviledge->position,
                    "anAdmin" => $this->memberWithoutAdminPriviledge->anAdmin,
                    "active" => $this->memberWithoutAdminPriviledge->active,
                    "joinTime" => $this->memberWithoutAdminPriviledge->joinTime,
                    "client" => [
                        "id" => $this->memberWithoutAdminPriviledge->client->id,
                        "name" => $this->memberWithoutAdminPriviledge->client->getFullName(),
                    ],
                ],
            ],
        ];
        $this->get($this->memberUri, $this->memberWithAdminPriviledge->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_requestFromNonAdminMember_403()
    {
        $this->get($this->memberUri, $this->memberWithoutAdminPriviledge->client->token)
                ->seeStatusCode(403);
    }
}

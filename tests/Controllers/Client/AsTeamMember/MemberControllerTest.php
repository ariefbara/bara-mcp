<?php

namespace Tests\Controllers\Client\AsTeamMember;

use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\Firm\ {
    RecordOfClient,
    Team\RecordOfMember
};

class MemberControllerTest extends AsTeamMemberTestCase
{
    protected $memberUri;
    protected $member;
    protected $memberOne_inactive;
    protected $client_nonMember;
    
    protected $addMemberInput;

    protected function setUp(): void
    {
        parent::setUp();
        $this->memberUri = $this->asTeamMemberUri . "/members";
        
        $team = $this->teamMember->team;
        $firm = $team->firm;
        
        $this->client_nonMember = new RecordOfClient($firm, 0);
        $this->connection->table("Client")->insert($this->client_nonMember->toArrayForDbEntry());
        
        $this->addMemberInput = [
            "clientId" => $this->client_nonMember->id,
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
                "id" => $this->client_nonMember->id,
                "name" => $this->client_nonMember->getFullName(),
            ],
        ];
        
        $this->post($this->memberUri, $this->addMemberInput, $this->teamMember->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
        
        $memberEntry = [
            "position" => $this->addMemberInput["memberPosition"],
            "anAdmin" => $this->addMemberInput["anAdmin"],
            "active" => true,
            "joinTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
            "Client_id" => $this->client_nonMember->id,
        ];
        $this->seeInDatabase("T_Member", $memberEntry);
    }
    public function test_add_clientIsInactiveMember_activateMember()
    {
        $this->addMemberInput["clientId"] = $this->teamMemberOne_inactive->client->id;
        $response = [
            "id" => $this->teamMemberOne_inactive->id,
            "position" => $this->addMemberInput["memberPosition"],
            "anAdmin" => $this->addMemberInput["anAdmin"],
            "active" => true,
            "joinTime" => $this->teamMemberOne_inactive->joinTime,
            "client" => [
                "id" => $this->teamMemberOne_inactive->client->id,
                "name" => $this->teamMemberOne_inactive->client->getFullName(),
            ],
        ];
        
        $this->post($this->memberUri, $this->addMemberInput, $this->teamMember->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
        
        $memberEntry = [
            "id" => $this->teamMemberOne_inactive->id,
            "position" => $this->addMemberInput["memberPosition"],
            "anAdmin" => $this->addMemberInput["anAdmin"],
            "active" => true,
            "joinTime" => $this->teamMemberOne_inactive->joinTime,
            "Client_id" => $this->teamMemberOne_inactive->client->id,
        ];
        $this->seeInDatabase("T_Member", $memberEntry);
    }
    public function test_add_clientAlreadyActiveMember_403()
    {
        $this->addMemberInput["clientId"] = $this->teamMemberTwo_notAdmin->client->id;
        $this->post($this->memberUri, $this->addMemberInput, $this->teamMember->client->token)
                ->seeStatusCode(403);
    }
    public function test_add_requestFromNonAdminMember_403()
    {
        $this->post($this->memberUri, $this->addMemberInput, $this->teamMemberTwo_notAdmin->client->token)
                ->seeStatusCode(403);
    }
    
    public function test_remove_200()
    {
        $uri = $this->memberUri . "/{$this->teamMemberTwo_notAdmin->id}";
        $this->delete($uri, [], $this->teamMember->client->token)
                ->seeStatusCode(200);
        
        $memberEntry = [
            "id" => $this->teamMemberTwo_notAdmin->id,
            "active" => false,
        ];
        $this->seeInDatabase("T_Member", $memberEntry);
    }
    public function test_remove_alreadyInactiveMember_403()
    {
        $uri = $this->memberUri . "/{$this->teamMemberOne_inactive->id}";
        $this->delete($uri, [], $this->teamMember->client->token)
                ->seeStatusCode(403);
    }
    public function test_remove_requestFromNonAdminMember_403()
    {
        $uri = $this->memberUri . "/{$this->teamMemberTwo_notAdmin->id}";
        $this->delete($uri, [], $this->teamMemberTwo_notAdmin->client->token)
                ->seeStatusCode(403);
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->teamMemberTwo_notAdmin->id,
            "position" => $this->teamMemberTwo_notAdmin->position,
            "anAdmin" => $this->teamMemberTwo_notAdmin->anAdmin,
            "active" => $this->teamMemberTwo_notAdmin->active,
            "joinTime" => $this->teamMemberTwo_notAdmin->joinTime,
            "client" => [
                "id" => $this->teamMemberTwo_notAdmin->client->id,
                "name" => $this->teamMemberTwo_notAdmin->client->getFullName(),
            ],
        ];
        $uri = $this->memberUri . "/{$this->teamMemberTwo_notAdmin->id}";
        $this->get($uri, $this->teamMember->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_show_requestFromInactiveMember_403()
    {
        $uri = $this->memberUri . "/{$this->teamMember->id}";
        $this->get($uri, $this->teamMemberOne_inactive->client->token)
                ->seeStatusCode(403);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 3,
            "list" => [
                [
                    "id" => $this->teamMember->id,
                    "position" => $this->teamMember->position,
                    "anAdmin" => $this->teamMember->anAdmin,
                    "active" => $this->teamMember->active,
                    "joinTime" => $this->teamMember->joinTime,
                    "client" => [
                        "id" => $this->teamMember->client->id,
                        "name" => $this->teamMember->client->getFullName(),
                    ],
                ],
                [
                    "id" => $this->teamMemberOne_inactive->id,
                    "position" => $this->teamMemberOne_inactive->position,
                    "anAdmin" => $this->teamMemberOne_inactive->anAdmin,
                    "active" => $this->teamMemberOne_inactive->active,
                    "joinTime" => $this->teamMemberOne_inactive->joinTime,
                    "client" => [
                        "id" => $this->teamMemberOne_inactive->client->id,
                        "name" => $this->teamMemberOne_inactive->client->getFullName(),
                    ],
                ],
                [
                    "id" => $this->teamMemberTwo_notAdmin->id,
                    "position" => $this->teamMemberTwo_notAdmin->position,
                    "anAdmin" => $this->teamMemberTwo_notAdmin->anAdmin,
                    "active" => $this->teamMemberTwo_notAdmin->active,
                    "joinTime" => $this->teamMemberTwo_notAdmin->joinTime,
                    "client" => [
                        "id" => $this->teamMemberTwo_notAdmin->client->id,
                        "name" => $this->teamMemberTwo_notAdmin->client->getFullName(),
                    ],
                ],
            ],
        ];
        $this->get($this->memberUri, $this->teamMember->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_requestFromNonAdminMember_403()
    {
        $this->get($this->memberUri, $this->teamMemberOne_inactive->client->token)
                ->seeStatusCode(403);
    }
}

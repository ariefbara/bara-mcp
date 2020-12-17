<?php

namespace Tests\Controllers\Client\AsTeamMember;

use Tests\Controllers\RecordPreparation\ {
    Firm\RecordOfClient,
    RecordOfFirm
};

class FindClientByEmailControllerTest extends AsTeamMemberTestCase
{
    protected $findClientByEmailUri;
    protected $client;
    protected $clientOne_fromOtherFirm;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->findClientByEmailUri = $this->asTeamMemberUri . "/find-client-by-email";
        
        $firm = $this->teamMember->team->firm;
        $otherFirm = new RecordOfFirm("otherFirm", "other-firm");
        $this->connection->table("Firm")->insert($otherFirm->toArrayForDbEntry());
        
        $this->client = new RecordOfClient($firm, 0);
        $this->client->email = "purnama.adi@gmail.com";
        $this->clientOne_fromOtherFirm = new RecordOfClient($otherFirm, 1);
        $this->connection->table("Client")->insert($this->client->toArrayForDbEntry());
        $this->connection->table("Client")->insert($this->clientOne_fromOtherFirm->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->client->id,
            "name" => $this->client->getFullName(),
            "email" => $this->client->email,
            "isActive" => $this->client->activated,
        ];
        $uri = $this->findClientByEmailUri . "/?clientEmail={$this->client->email}";
        $this->get($uri, $this->teamMember->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_show_clientFromOtherFirm_404()
    {
        $uri = $this->findClientByEmailUri . "/?clientEmail={$this->clientOne_fromOtherFirm->email}";
        $this->get($uri, $this->teamMember->client->token)
                ->seeStatusCode(404);
    }
    public function test_show_notTeamAdmin_403()
    {
        $uri = $this->findClientByEmailUri . "/?clientEmail={$this->client->email}";
        $this->get($uri, $this->teamMemberTwo_notAdmin->client->token)
                ->seeStatusCode(403);
    }
    public function test_show_inactiveMember_403()
    {
        $uri = $this->findClientByEmailUri . "/?clientEmail={$this->client->email}";
        $this->get($uri, $this->teamMemberOne_inactive->client->token)
                ->seeStatusCode(403);
    }
    
}

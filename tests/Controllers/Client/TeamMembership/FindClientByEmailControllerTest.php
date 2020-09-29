<?php

namespace Tests\Controllers\Client\TeamMembership;

use Tests\Controllers\ {
    Client\TeamMembershipTestCase,
    RecordPreparation\Firm\RecordOfClient,
    RecordPreparation\RecordOfFirm
};

class FindClientByEmailControllerTest extends TeamMembershipTestCase
{
    protected $findClientByEmailUri;
    protected $client;
    protected $clientOne_fromOtherFirm;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->findClientByEmailUri = $this->teamMembershipUri . "/find-client-by-email";
        
        $firm = $this->teamMembership->client->firm;
        $otherFirm = new RecordOfFirm("otherFirm", "other-firm");
        $this->connection->table("Firm")->insert($otherFirm->toArrayForDbEntry());
        
        $this->client = new RecordOfClient($firm, 0);
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
        $this->get($uri, $this->teamMembership->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_show_clientFromOtherFirm_404()
    {
        $uri = $this->findClientByEmailUri . "/?clientEmail={$this->clientOne_fromOtherFirm->email}";
        $this->get($uri, $this->teamMembership->client->token)
                ->seeStatusCode(404);
    }
    public function test_show_notTeamAdmin_403()
    {
        $this->setTeamMembershipNotAnAdmin();
        $uri = $this->findClientByEmailUri . "/?clientEmail={$this->client->email}";
        $this->get($uri, $this->teamMembership->client->token)
                ->seeStatusCode(403);
    }
    public function test_show_inactiveMembership_403()
    {
        $this->setTeamMembershipInactive();
        $uri = $this->findClientByEmailUri . "/?clientEmail={$this->client->email}";
        $this->get($uri, $this->teamMembership->client->token)
                ->seeStatusCode(403);
    }
    
}

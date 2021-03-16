<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;

class ClientControllerTest extends ManagerTestCase
{
    protected $clientUri;
    protected $clientOne;
    protected $clientTwo_inactive;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientUri = $this->managerUri . "/clients";
        $this->connection->table("Client")->truncate();
        
        $firm = $this->manager->firm;
        
        $this->clientOne = new RecordOfClient($firm, 1);
        $this->clientTwo_inactive = new RecordOfClient($firm, 2);
        $this->clientTwo_inactive->activated = false;
        $this->connection->table("Client")->insert($this->clientOne->toArrayForDbEntry());
        $this->connection->table("Client")->insert($this->clientTwo_inactive->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Client")->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->clientOne->id,
            "name" => $this->clientOne->getFullName(),
            "email" => $this->clientOne->email,
            "signupTime" => $this->clientOne->signupTime,
            "activated" => $this->clientOne->activated,
        ];
        $uri = $this->clientUri . "/{$this->clientOne->id}";
        $this->get($uri, $this->manager->token);
        $this->seeStatusCode(200);
        $this->seeJsonContains($response);
    }
    
    public function test_showAll_200()
    {
        $this->get($this->clientUri, $this->manager->token);
        $this->seeStatusCode(200);
        
        $totalResponse = ["total" => 2];
        $this->seeJsonContains($totalResponse);
        
        $clientOneResponse = [
            "id" => $this->clientOne->id,
            "name" => $this->clientOne->getFullName(),
            "email" => $this->clientOne->email,
            "signupTime" => $this->clientOne->signupTime,
            "activated" => $this->clientOne->activated,
        ];
        $this->seeJsonContains($clientOneResponse);
        $clientTwoResponse = [
            "id" => $this->clientTwo_inactive->id,
        ];
        $this->seeJsonContains($clientTwoResponse);
    }
    public function test_showAll_queryFilterSet()
    {
        $uri = $this->clientUri . "?activatedStatus=true";
        $this->get($uri, $this->manager->token);
        $totalResponse = ["total" => 1];
        $this->seeJsonContains($totalResponse);
        
        $clientOneResponse = [
            "id" => $this->clientOne->id,
            "name" => $this->clientOne->getFullName(),
            "email" => $this->clientOne->email,
            "signupTime" => $this->clientOne->signupTime,
            "activated" => $this->clientOne->activated,
        ];
        $this->seeJsonContains($clientOneResponse);
    }
}

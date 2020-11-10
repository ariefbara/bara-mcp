<?php

namespace Tests\Controllers\Client;

use Tests\Controllers\RecordPreparation\Firm\RecordOfManager;

class ManagerControllerTest extends ClientTestCase
{
    protected $managerUri;
    protected $manager;
    protected $managerOne;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->managerUri = $this->clientUri . "/managers";
        $this->connection->table("Manager")->truncate();
        
        $firm = $this->client->firm;
        
        $this->manager = new RecordOfManager($firm, 0, "manager@email.org", "Password123");
        $this->managerOne = new RecordOfManager($firm, 1, "manager1@email.org", "Password123");
        $this->connection->table("Manager")->insert($this->manager->toArrayForDbEntry());
        $this->connection->table("Manager")->insert($this->managerOne->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Manager")->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->manager->id,
            "name" => $this->manager->name,
            "removed" => $this->manager->removed,
        ];
        $uri = $this->managerUri . "/{$this->manager->id}";
        $this->get($uri, $this->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_show_inactiveClient_403()
    {
        $uri = $this->managerUri . "/{$this->manager->id}";
        $this->get($uri, $this->inactiveClient->token)
                ->seeStatusCode(403);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->manager->id,
                    "name" => $this->manager->name,
                    "removed" => $this->manager->removed,
                ],
                [
                    "id" => $this->managerOne->id,
                    "name" => $this->managerOne->name,
                    "removed" => $this->managerOne->removed,
                ],
            ],
        ];
        
        $this->get($this->managerUri, $this->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_inactiveClient_403()
    {
        $this->get($this->managerUri, $this->inactiveClient->token)
                ->seeStatusCode(403);
    }
    
}

<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\Firm\RecordOfManager;

class ManagerControllerTest extends ManagerTestCase
{
    protected $uri;
    protected $managerOne;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->uri = $this->managerUri . "/managers";
        
        $firm = $this->manager->firm;
        
        $this->managerOne = new RecordOfManager($firm, 1, "manager1@email.org", "Password123");
        $this->connection->table("Manager")->insert($this->managerOne->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->managerOne->id,
            "name" => $this->managerOne->name,
            "removed" => $this->managerOne->removed,
        ];
        $uri = $this->uri . "/{$this->managerOne->id}";
        $this->get($uri, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_show_inactiveManager_401()
    {
        $uri = $this->uri . "/{$this->manager->id}";
        $this->get($uri, $this->removedManager->token)
                ->seeStatusCode(401);
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
        
        $this->get($this->uri, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_inactiveManager_401()
    {
        $this->get($this->uri, $this->removedManager->token)
                ->seeStatusCode(401);
    }
    
}

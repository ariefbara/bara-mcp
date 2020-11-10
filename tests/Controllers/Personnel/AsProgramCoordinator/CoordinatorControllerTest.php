<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

use Tests\Controllers\RecordPreparation\Firm\ {
    Program\RecordOfCoordinator,
    RecordOfPersonnel
};

class CoordinatorControllerTest extends AsProgramCoordinatorTestCase
{
    protected $coordinatorUri;
    protected $coordinator;
    protected $coordinatorOne;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinatorUri = $this->asProgramCoordinatorUri . "/coordinators";
        
        $program = $this->coordinator->program;
        $firm = $program->firm;
        
        $personnelOne = new RecordOfPersonnel($firm, 1);
        $this->connection->table("Personnel")->insert($personnelOne->toArrayForDbEntry());
        
        $this->coordinatorOne = new RecordOfCoordinator($program, $personnelOne, 1);
        $this->connection->table("Coordinator")->insert($this->coordinatorOne->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->coordinator->id,
            "personnel" => [
                "id" => $this->coordinator->personnel->id,
                "name" => $this->coordinator->personnel->getFullName(),
            ],
        ];
        
        $uri = $this->coordinatorUri . "/{$this->coordinator->id}";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_show_inactiveCoordinator_403()
    {
        $uri = $this->coordinatorUri . "/{$this->coordinator->id}";
        $this->get($uri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(403);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->coordinator->id,
                    "personnel" => [
                        "id" => $this->coordinator->personnel->id,
                        "name" => $this->coordinator->personnel->getFullName(),
                    ],
                ],
                [
                    "id" => $this->coordinatorOne->id,
                    "personnel" => [
                        "id" => $this->coordinatorOne->personnel->id,
                        "name" => $this->coordinatorOne->personnel->getFullName(),
                    ],
                ],
            ],
        ];
        
        $this->get($this->coordinatorUri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_inactiveCoordinator_403()
    {
        $this->get($this->coordinatorUri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(403);
    }
}

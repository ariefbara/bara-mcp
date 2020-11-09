<?php

namespace Tests\Controllers\Personnel\AsProgramConsultant;

use Tests\Controllers\RecordPreparation\Firm\ {
    Program\RecordOfCoordinator,
    RecordOfPersonnel
};

class CoordinatorControllerTest extends AsProgramConsultantTestCase
{
    protected $coordinatorUri;
    protected $coordinator;
    protected $coordinatorOne;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinatorUri = $this->asProgramConsultantUri . "/coordinators";
        
        $this->connection->table("Coordinator")->truncate(); 
        
        $program = $this->consultant->program;
        $firm = $program->firm;
        
        $personnel = new RecordOfPersonnel($firm, 0);
        $personnelOne = new RecordOfPersonnel($firm, 1);
        $this->connection->table("Personnel")->insert($personnel->toArrayForDbEntry());
        $this->connection->table("Personnel")->insert($personnelOne->toArrayForDbEntry());
        
        $this->coordinator = new RecordOfCoordinator($program, $personnel, 0);
        $this->coordinatorOne = new RecordOfCoordinator($program, $personnelOne, 1);
        $this->connection->table("Coordinator")->insert($this->coordinator->toArrayForDbEntry());
        $this->connection->table("Coordinator")->insert($this->coordinatorOne->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Coordinator")->truncate(); 
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
        $this->get($uri, $this->consultant->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_show_inactiveConsultant_401()
    {
        $uri = $this->coordinatorUri . "/{$this->coordinator->id}";
        $this->get($uri, $this->removedConsultant->personnel->token)
                ->seeStatusCode(401);
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
        
        $this->get($this->coordinatorUri, $this->consultant->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_inactiveConsultant_401()
    {
        $this->get($this->coordinatorUri, $this->removedConsultant->personnel->token)
                ->seeStatusCode(401);
    }
}

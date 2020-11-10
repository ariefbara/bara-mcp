<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

use Tests\Controllers\RecordPreparation\Firm\ {
    Program\RecordOfConsultant,
    RecordOfPersonnel
};

class ConsultantControllerTest extends AsProgramCoordinatorTestCase
{
    protected $consultantUri;
    protected $consultantOne;
    protected $consultantTwo;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultantUri = $this->asProgramCoordinatorUri . "/consultants";
        $this->connection->table("Consultant")->truncate();
        
        $program = $this->coordinator->program;
        $firm = $program->firm;
        
        $personnelOne = new RecordOfPersonnel($firm, 1);
        $personnelTwo = new RecordOfPersonnel($firm, 2);
        $this->connection->table("Personnel")->insert($personnelOne->toArrayForDbEntry());
        $this->connection->table("Personnel")->insert($personnelTwo->toArrayForDbEntry());
        
        $this->consultantOne = new RecordOfConsultant($program, $personnelOne, 1);
        $this->consultantTwo = new RecordOfConsultant($program, $personnelTwo, 2);
        $this->connection->table("Consultant")->insert($this->consultantOne->toArrayForDbEntry());
        $this->connection->table("Consultant")->insert($this->consultantTwo->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Consultant")->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->consultantOne->id,
            "removed" => $this->consultantOne->removed,
            "personnel" => [
                "id" => $this->consultantOne->personnel->id,
                "name" => $this->consultantOne->personnel->getFullName(),
            ],
        ];
        $uri = $this->consultantUri . "/{$this->consultantOne->id}";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_show_inactiveConsultant_403()
    {
        $uri = $this->consultantUri . "/{$this->consultantOne->id}";
        $this->get($uri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(403);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->consultantOne->id,
                    "removed" => $this->consultantOne->removed,
                    "personnel" => [
                        "id" => $this->consultantOne->personnel->id,
                        "name" => $this->consultantOne->personnel->getFullName(),
                    ],
                ],
                [
                    "id" => $this->consultantTwo->id,
                    "removed" => $this->consultantTwo->removed,
                    "personnel" => [
                        "id" => $this->consultantTwo->personnel->id,
                        "name" => $this->consultantTwo->personnel->getFullName(),
                    ],
                ],
            ],
        ];
        
        $this->get($this->consultantUri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_inactiveConsultant_403()
    {
        $this->get($this->consultantUri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(403);
    }
}

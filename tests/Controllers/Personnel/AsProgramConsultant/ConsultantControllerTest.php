<?php

namespace Tests\Controllers\Personnel\AsProgramConsultant;

use Tests\Controllers\RecordPreparation\Firm\ {
    Program\RecordOfConsultant,
    RecordOfPersonnel
};

class ConsultantControllerTest extends AsProgramConsultantTestCase
{
    protected $consultantUri;
    protected $consultantOne;
    protected $consultantTwo;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultantUri = $this->asProgramConsultantUri . "/consultants";
        
        $program = $this->consultant->program;
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
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->consultantOne->id,
            "personnel" => [
                "id" => $this->consultantOne->personnel->id,
                "name" => $this->consultantOne->personnel->getFullName(),
            ],
        ];
        $uri = $this->consultantUri . "/{$this->consultantOne->id}";
        $this->get($uri, $this->consultant->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_show_inactiveConsultant_401()
    {
        $uri = $this->consultantUri . "/{$this->consultantOne->id}";
        $this->get($uri, $this->removedConsultant->personnel->token)
                ->seeStatusCode(401);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 3,
            "list" => [
                [
                    "id" => $this->consultant->id,
                    "personnel" => [
                        "id" => $this->consultant->personnel->id,
                        "name" => $this->consultant->personnel->getFullName(),
                    ],
                ],
                [
                    "id" => $this->consultantOne->id,
                    "personnel" => [
                        "id" => $this->consultantOne->personnel->id,
                        "name" => $this->consultantOne->personnel->getFullName(),
                    ],
                ],
                [
                    "id" => $this->consultantTwo->id,
                    "personnel" => [
                        "id" => $this->consultantTwo->personnel->id,
                        "name" => $this->consultantTwo->personnel->getFullName(),
                    ],
                ],
            ],
        ];
        
        $this->get($this->consultantUri, $this->consultant->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_inactiveConsultant_401()
    {
        $this->get($this->consultantUri, $this->removedConsultant->personnel->token)
                ->seeStatusCode(401);
    }
}

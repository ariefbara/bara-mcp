<?php

namespace Tests\Controllers\Client\AsProgramParticipant;

use Tests\Controllers\RecordPreparation\Firm\ {
    Program\RecordOfConsultant,
    RecordOfPersonnel
};

class ConsultantControllerTest extends AsProgramParticipantTestCase
{
    protected $consultantUri;
    protected $consultant;
    protected $consultantOne;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultantUri = $this->asProgramParticipantUri . "/consultants";
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        
        $participant = $this->programParticipation->participant;
        $program = $participant->program;
        $firm = $program->firm;
        
        $personnel = new RecordOfPersonnel($firm, 0, 'personnel@email.org', 'password123');
        $personnelOne = new RecordOfPersonnel($firm, 1, 'personnelOne@email.org', 'password123');
        $this->connection->table('Personnel')->insert($personnel->toArrayForDbEntry());
        $this->connection->table('Personnel')->insert($personnelOne->toArrayForDbEntry());
        
        $this->consultant = new RecordOfConsultant($program, $personnel, 0);
        $this->consultantOne = new RecordOfConsultant($program, $personnelOne, 1);
        $this->connection->table('Consultant')->insert($this->consultant->toArrayForDbEntry());
        $this->connection->table('Consultant')->insert($this->consultantOne->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->consultant->id,
            'personnel' => [
                "id" => $this->consultant->personnel->id,
                "name" => $this->consultant->personnel->getFullName(),
            ],
        ];
        $uri = $this->consultantUri . "/{$this->consultant->id}";
        $this->get($uri, $this->programParticipation->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_show_clientNotActiveParticipant_403()
    {
        $uri = $this->consultantUri . "/{$this->consultant->id}";
        $this->get($uri, $this->inactiveProgramParticipation->client->token)
                ->seeStatusCode(403);
        
    }
    
    public function test_showAll()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->consultant->id,
                    'personnel' => [
                        "id" => $this->consultant->personnel->id,
                        "name" => $this->consultant->personnel->getFullName(),
                    ],
                ],
                [
                    "id" => $this->consultantOne->id,
                    'personnel' => [
                        "id" => $this->consultantOne->personnel->id,
                        "name" => $this->consultantOne->personnel->getFullName(),
                    ],
                ],
            ],
        ];
        $this->get($this->consultantUri, $this->programParticipation->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_showAll_ClientNotActiveParticipant_403()
    {
        $this->get($this->consultantUri, $this->inactiveProgramParticipation->client->token)
                ->seeStatusCode(403);
    }
}

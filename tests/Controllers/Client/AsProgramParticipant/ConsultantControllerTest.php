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
        $this->consultantUri = $this->asProgramparticipantUri . "/consultants";
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        
        $personnel = new RecordOfPersonnel($this->participant->program->firm, 0, 'personnel@email.org', 'password123');
        $personnelOne = new RecordOfPersonnel($this->participant->program->firm, 1, 'personnelOne@email.org', 'password123');
        $this->connection->table('Personnel')->insert($personnel->toArrayForDbEntry());
        $this->connection->table('Personnel')->insert($personnelOne->toArrayForDbEntry());
        
        $this->consultant = new RecordOfConsultant($this->participant->program, $personnel, 0);
        $this->consultantOne = new RecordOfConsultant($this->participant->program, $personnelOne, 1);
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
                "name" => $this->consultant->personnel->name,
            ],
        ];
        $uri = $this->consultantUri . "/{$this->consultant->id}";
        $this->get($uri, $this->participant->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_show_clientNotActiveParticipant_error401()
    {
        $uri = $this->consultantUri . "/{$this->consultant->id}";
        $this->get($uri, $this->inactiveParticipant->client->token)
                ->seeStatusCode(401);
        
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
                        "name" => $this->consultant->personnel->name,
                    ],
                ],
                [
                    "id" => $this->consultantOne->id,
                    'personnel' => [
                        "id" => $this->consultantOne->personnel->id,
                        "name" => $this->consultantOne->personnel->name,
                    ],
                ],
            ],
        ];
        $this->get($this->consultantUri, $this->participant->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_showAll_ClientNotActiveParticipant_error401()
    {
        $this->get($this->consultantUri, $this->inactiveParticipant->client->token)
                ->seeStatusCode(401);
    }
}

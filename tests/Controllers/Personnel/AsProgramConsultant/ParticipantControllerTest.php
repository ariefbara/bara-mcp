<?php

namespace Tests\Controllers\Personnel\AsProgramConsultant;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfParticipant,
    RecordOfClient
};

class ParticipantControllerTest extends ParticipantTestCase
{
    protected $participantOne;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $client = new RecordOfClient(1, 'clientOne@email.org', 'password123');
        $this->connection->table('Client')->insert($client->toArrayForDbEntry());
        
        $this->participantOne = new RecordOfParticipant($this->consultant->program, $client, 1);
        $this->connection->table('Participant')->insert($this->participantOne->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->participant->id,
            "acceptedTime" => $this->participant->acceptedTime,
            "active" => $this->participant->active,
            "note" => $this->participant->note,
            "client" => [
                "id" => $this->participant->client->id,
                "name" => $this->participant->client->name,
            ],
        ];
        
        $uri = $this->participantUri . "/{$this->participant->id}";
        $this->get($uri, $this->consultant->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_show_personnelNotProgramConsultant_error401()
    {
        $uri = $this->participantUri . "/{$this->participant->id}";
        $this->get($uri, $this->removedConsultant->personnel->token)
                ->seeStatusCode(401);
        
    }
    
    public function test_showAll()
    {
        $response = [
            "total" => 2, 
            "list" => [
                [
                    "id" => $this->participant->id,
                    "acceptedTime" => $this->participant->acceptedTime,
                    "active" => $this->participant->active,
                    "note" => $this->participant->note,
                    "client" => [
                        "id" => $this->participant->client->id,
                        "name" => $this->participant->client->name,
                    ],
                ],
                [
                    "id" => $this->participantOne->id,
                    "acceptedTime" => $this->participantOne->acceptedTime,
                    "active" => $this->participantOne->active,
                    "note" => $this->participantOne->note,
                    "client" => [
                        "id" => $this->participantOne->client->id,
                        "name" => $this->participantOne->client->name,
                    ],
                ],
            ],
        ];
        $this->get($this->participantUri, $this->consultant->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_showAll_personnelNotProgramConsultant_error401()
    {
        $this->get($this->participantUri, $this->removedConsultant->personnel->token)
                ->seeStatusCode(401);
    }
}

<?php

namespace Tests\Controllers\Client;

use Tests\Controllers\RecordPreparation\Firm\ {
    Client\RecordOfClientParticipant,
    Program\RecordOfParticipant,
    RecordOfProgram
};


class ProgramParticipationControllerTest extends ProgramParticipationTestCase
{
    protected $inactiveProgramParticipation;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $program = new RecordOfProgram($this->client->firm, 1);
        $this->connection->table('Program')->insert($program->toArrayForDbEntry());
        
        $participant = new RecordOfParticipant($program, 1);
        $participant->active = false;
        $participant->note = 'quit';
        $this->connection->table('Participant')->insert($participant->toArrayForDbEntry());
        
        $this->inactiveProgramParticipation = new RecordOfClientParticipant($this->client, $participant);
        $this->connection->table('ClientParticipant')->insert($this->inactiveProgramParticipation->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_quit_200()
    {
$this->disableExceptionHandling();
        $uri = $this->programParticipationUri . "/{$this->programParticipation->id}/quit";
        $this->patch($uri, [], $this->client->token)
            ->seeStatusCode(200);
        
        $participantEntry = [
            "id" => $this->programParticipation->participant->id,
            "active" => false,
            "note" => 'quit',
        ];
        $this->seeInDatabase('Participant', $participantEntry);
    }
    public function test_quit_alreadyInactive_403()
    {
        $uri = $this->programParticipationUri . "/{$this->inactiveProgramParticipation->id}/quit";
        $this->patch($uri, [], $this->client->token)
            ->seeStatusCode(403);
    }
    public function test_show()
    {
        $response = [
            "program" => [
                "id" => $this->programParticipation->participant->program->id,
                "name" => $this->programParticipation->participant->program->name,
                "removed" => $this->programParticipation->participant->program->removed,
            ],
            "enrolledTime" => $this->programParticipation->participant->enrolledTime,
            "active" => $this->programParticipation->participant->active,
            "note" => $this->programParticipation->participant->note,
        ];
        $uri = $this->programParticipationUri . "/{$this->programParticipation->id}";
        $this->get($uri, $this->client->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    
    public function test_showAll()
    {
        $response = [
            "total" => 2, 
            "list" => [
                [
                    "program" => [
                        "id" => $this->programParticipation->participant->program->id,
                        "name" => $this->programParticipation->participant->program->name,
                        "removed" => $this->programParticipation->participant->program->removed,
                    ],
                    "enrolledTime" => $this->programParticipation->participant->enrolledTime,
                    "active" => $this->programParticipation->participant->active,
                    "note" => $this->programParticipation->participant->note,
                ],
                [
                    "program" => [
                        "id" => $this->inactiveProgramParticipation->participant->program->id,
                        "name" => $this->inactiveProgramParticipation->participant->program->name,
                        "removed" => $this->inactiveProgramParticipation->participant->program->removed,
                    ],
                    "enrolledTime" => $this->inactiveProgramParticipation->participant->enrolledTime,
                    "active" => $this->inactiveProgramParticipation->participant->active,
                    "note" => $this->inactiveProgramParticipation->participant->note,
                ],
            ],
        ];
        $this->get($this->programParticipationUri, $this->client->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
}

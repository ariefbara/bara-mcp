<?php

namespace Tests\Controllers\Client;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfProgram,
    RecordOfFirm
};

class ProgramParticipationControllerTest extends ProgramParticipationTestCase
{
    protected $programParticipationOne;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $firm = new RecordOfFirm(0, 'firm_1');
        $this->connection->table('Firm')->insert($firm->toArrayForDbEntry());
        
        $program = new RecordOfProgram($firm, 1);
        $this->connection->table('Program')->insert($program->toArrayForDbEntry());
        
        $this->programParticipationOne = new RecordOfParticipant($program, $this->client, 1);
        $this->connection->table('Participant')->insert($this->programParticipationOne->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_quit()
    {
        $uri = $this->programParticipationUri . "/{$this->programParticipation->id}/quit";
        $this->patch($uri, [], $this->client->token)
            ->seeStatusCode(200);
        
        $programParticipationEntry = [
            "id" => $this->programParticipation->id,
            "active" => false,
            "note" => 'quit',
        ];
        $this->seeInDatabase('Participant', $programParticipationEntry);
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->programParticipation->id,
            "program" => [
                "id" => $this->programParticipation->program->id,
                "name" => $this->programParticipation->program->name,
                "removed" => $this->programParticipation->program->removed,
                "firm" => [
                    "id" => $this->programParticipation->program->firm->id,
                    "name" => $this->programParticipation->program->firm->name,
                    
                ],
            ],
            "acceptedTime" => $this->programParticipation->acceptedTime,
            "active" => $this->programParticipation->active,
            "note" => $this->programParticipation->note,
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
                    "id" => $this->programParticipation->id,
                    "note" => $this->programParticipation->note,
                    "active" => $this->programParticipation->active,
                    "program" => [
                        "id" => $this->programParticipation->program->id,
                        "name" => $this->programParticipation->program->name,
                        "removed" => $this->programParticipation->program->removed,
                    ],
                ],
                [
                    "id" => $this->programParticipationOne->id,
                    "note" => $this->programParticipationOne->note,
                    "active" => $this->programParticipationOne->active,
                    "program" => [
                        "id" => $this->programParticipationOne->program->id,
                        "name" => $this->programParticipationOne->program->name,
                        "removed" => $this->programParticipationOne->program->removed,
                    ],
                ],
            ],
        ];
        $this->get($this->programParticipationUri, $this->client->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
}

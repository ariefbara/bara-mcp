<?php

namespace Tests\Controllers\Client;

use Query\Domain\Model\Firm\ParticipantTypes;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;

class ProgramControllerTest extends ClientTestCase
{
    protected $programUri;
    protected $program;
    protected $programOne_forClientType;
    protected $programTwo_forUserType;
    protected $programThree_forTeamType;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programUri = $this->clientUri . "/programs";
        
        $this->connection->table("Program")->truncate();
        
        $firm = $this->client->firm;
        
        $this->program = new RecordOfProgram($firm, 0);
        $this->programOne_forClientType = new RecordOfProgram($firm, 1);
        $this->programOne_forClientType->participantTypes = ParticipantTypes::CLIENT_TYPE;
        $this->programTwo_forUserType = new RecordOfProgram($firm, 2);
        $this->programTwo_forUserType->participantTypes = ParticipantTypes::USER_TYPE;
        $this->programThree_forTeamType = new RecordOfProgram($firm, 3);
        $this->programThree_forTeamType->participantTypes = ParticipantTypes::TEAM_TYPE;
        $this->connection->table("Program")->insert($this->program->toArrayForDbEntry());
        $this->connection->table("Program")->insert($this->programOne_forClientType->toArrayForDbEntry());
        $this->connection->table("Program")->insert($this->programTwo_forUserType->toArrayForDbEntry());
        $this->connection->table("Program")->insert($this->programThree_forTeamType->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Program")->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->program->id,
            "name" => $this->program->name,
            "description" => $this->program->description,
            "published" => $this->program->published,
            "participantTypes" => explode(",", $this->program->participantTypes),
            "removed" => $this->program->removed,
        ];
        $uri = $this->programUri . "/{$this->program->id}";
        $this->get($uri, $this->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    
    public function test_showAll_200_returnProgramContainClientPartipantType()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->program->id,
                    "name" => $this->program->name,
                    "published" => $this->program->published,
                    "participantTypes" => explode(",", $this->program->participantTypes),
                    "removed" => $this->program->removed,
                ],
                [
                    "id" => $this->programOne_forClientType->id,
                    "name" => $this->programOne_forClientType->name,
                    "published" => $this->programOne_forClientType->published,
                    "participantTypes" => explode(",", $this->programOne_forClientType->participantTypes),
                    "removed" => $this->programOne_forClientType->removed,
                ],
            ],
        ];
        $this->get($this->programUri, $this->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAllProgramForTeam_200_returnAllProgramForTeamType()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->program->id,
                    "name" => $this->program->name,
                    "published" => $this->program->published,
                    "participantTypes" => explode(",", $this->program->participantTypes),
                    "removed" => $this->program->removed,
                ],
                [
                    "id" => $this->programThree_forTeamType->id,
                    "name" => $this->programThree_forTeamType->name,
                    "published" => $this->programThree_forTeamType->published,
                    "participantTypes" => explode(",", $this->programThree_forTeamType->participantTypes),
                    "removed" => $this->programThree_forTeamType->removed,
                ],
            ],
        ];
        $uri = $this->programUri . "/team-types";
        $this->get($uri, $this->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
        
    }
}

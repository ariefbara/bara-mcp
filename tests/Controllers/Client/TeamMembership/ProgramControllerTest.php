<?php

namespace Tests\Controllers\Client\TeamMembership;

use Query\Domain\Model\Firm\ParticipantTypes;
use Tests\Controllers\ {
    Client\TeamMembershipTestCase,
    RecordPreparation\Firm\RecordOfProgram
};

class ProgramControllerTest extends TeamMembershipTestCase
{
    protected $programUri;
    protected $program;
    protected $programOne_forTeamType;
    protected $programTwo_forUserType;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programUri = $this->teamMembershipUri . "/programs";
        
        $this->connection->table("Program")->truncate();
        
        $firm = $this->client->firm;
        
        $this->program = new RecordOfProgram($firm, 0);
        $this->programOne_forTeamType = new RecordOfProgram($firm, 1);
        $this->programOne_forTeamType->participantTypes = ParticipantTypes::TEAM_TYPE;
        $this->programTwo_forUserType = new RecordOfProgram($firm, 2);
        $this->programTwo_forUserType->participantTypes = ParticipantTypes::USER_TYPE;
        $this->connection->table("Program")->insert($this->program->toArrayForDbEntry());
        $this->connection->table("Program")->insert($this->programOne_forTeamType->toArrayForDbEntry());
        $this->connection->table("Program")->insert($this->programTwo_forUserType->toArrayForDbEntry());
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
    public function test_show_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        $uri = $this->programUri . "/{$this->program->id}";
        $this->get($uri, $this->client->token)
                ->seeStatusCode(403);
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
                    "removed" => $this->program->removed,
                ],
                [
                    "id" => $this->programOne_forTeamType->id,
                    "name" => $this->programOne_forTeamType->name,
                    "published" => $this->programOne_forTeamType->published,
                    "removed" => $this->programOne_forTeamType->removed,
                ],
            ],
        ];
        $this->get($this->programUri, $this->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        $this->get($this->programUri, $this->client->token)
                ->seeStatusCode(403);
    }
}

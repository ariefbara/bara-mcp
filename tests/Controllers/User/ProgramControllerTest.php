<?php

namespace Tests\Controllers\User;

use Query\Domain\Model\Firm\ParticipantTypes;
use Tests\Controllers\RecordPreparation\ {
    Firm\RecordOfProgram,
    RecordOfFirm
};

class ProgramControllerTest extends UserTestCase
{
    protected $programUri;
    protected $program;
    protected $programOne_forClientType;
    protected $programTwo_forUserType;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programUri = $this->userUri . "/programs";
        
        $this->connection->table("Program")->truncate();
        $this->connection->table("Firm")->truncate();
        
        $firm = new RecordOfFirm(0, 'firm-identifier');
        $this->connection->table("Firm")->insert($firm->toArrayForDbEntry());
        
        $this->program = new RecordOfProgram($firm, 0);
        $this->programOne_forClientType = new RecordOfProgram($firm, 1);
        $this->programOne_forClientType->participantTypes = ParticipantTypes::CLIENT_TYPE;
        $this->programTwo_forUserType = new RecordOfProgram($firm, 2);
        $this->programTwo_forUserType->participantTypes = ParticipantTypes::USER_TYPE;
        $this->connection->table("Program")->insert($this->program->toArrayForDbEntry());
        $this->connection->table("Program")->insert($this->programOne_forClientType->toArrayForDbEntry());
        $this->connection->table("Program")->insert($this->programTwo_forUserType->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Program")->truncate();
        $this->connection->table("Firm")->truncate();
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
            "firm" => [
                "id" => $this->program->firm->id,
                "name" => $this->program->firm->name,
            ],
        ];
        $uri = $this->programUri . "/{$this->program->firm->id}/{$this->program->id}";
        $this->get($uri, $this->user->token)
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
                    "removed" => $this->program->removed,
                    "firm" => [
                        "id" => $this->program->firm->id,
                        "name" => $this->program->firm->name,
                    ],
                ],
                [
                    "id" => $this->programTwo_forUserType->id,
                    "name" => $this->programTwo_forUserType->name,
                    "published" => $this->programTwo_forUserType->published,
                    "removed" => $this->programTwo_forUserType->removed,
                    "firm" => [
                        "id" => $this->programTwo_forUserType->firm->id,
                        "name" => $this->programTwo_forUserType->firm->name,
                    ],
                ],
            ],
        ];
        $this->get($this->programUri, $this->user->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}

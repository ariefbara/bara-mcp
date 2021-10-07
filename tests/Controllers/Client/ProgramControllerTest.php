<?php

namespace Tests\Controllers\Client;

use Query\Domain\Model\Firm\ParticipantTypes;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFirmFileInfo;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFileInfo;

class ProgramControllerTest extends ClientTestCase
{
    protected $programUri;
    protected $program;
    protected $programOne_forClientType;
    protected $programTwo_forUserType;
    protected $programThree_forTeamType;
    
    protected $firmFileInfo;
    protected $firmFileInfoOne;
    protected $firmFileInfoThree;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table("Program")->truncate();
        $this->connection->table("FileInfo")->truncate();
        $this->connection->table("FirmFileInfo")->truncate();
        
        $this->programUri = $this->clientUri . "/programs";
        
        $firm = $this->client->firm;
        
        $fileInfo = new RecordOfFileInfo("99");
        $fileInfoOne = new RecordOfFileInfo("1");
        $fileInfoThree = new RecordOfFileInfo("3");
        
        $this->firmFileInfo = new RecordOfFirmFileInfo($firm, $fileInfo);
        $this->firmFileInfoOne = new RecordOfFirmFileInfo($firm, $fileInfoOne);
        $this->firmFileInfoThree = new RecordOfFirmFileInfo($firm, $fileInfoThree);
        $this->firmFileInfo->insert($this->connection);
        $this->firmFileInfoOne->insert($this->connection);
        $this->firmFileInfoThree->insert($this->connection);
        
        $this->program = new RecordOfProgram($firm, 0);
        $this->program->illustration = $this->firmFileInfo;
        $this->program->published = true;
        $this->programOne_forClientType = new RecordOfProgram($firm, 1);
        $this->programOne_forClientType->illustration = $this->firmFileInfoOne;
        $this->programOne_forClientType->participantTypes = ParticipantTypes::CLIENT_TYPE;
        $this->programOne_forClientType->published = true;
        $this->programTwo_forUserType = new RecordOfProgram($firm, 2);
        $this->programTwo_forUserType->participantTypes = ParticipantTypes::USER_TYPE;
        $this->programThree_forTeamType = new RecordOfProgram($firm, 3);
        $this->programThree_forTeamType->illustration = $this->firmFileInfoThree;
        $this->programThree_forTeamType->participantTypes = ParticipantTypes::TEAM_TYPE;
        $this->programThree_forTeamType->published = true;
        $this->connection->table("Program")->insert($this->program->toArrayForDbEntry());
        $this->connection->table("Program")->insert($this->programOne_forClientType->toArrayForDbEntry());
        $this->connection->table("Program")->insert($this->programTwo_forUserType->toArrayForDbEntry());
        $this->connection->table("Program")->insert($this->programThree_forTeamType->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Program")->truncate();
        $this->connection->table("FileInfo")->truncate();
        $this->connection->table("FirmFileInfo")->truncate();
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
            "illustration" => [
                "id" => $this->firmFileInfo->id,
                "url" => "/{$this->firmFileInfo->fileInfo->name}",
            ],
        ];
        $uri = $this->programUri . "/{$this->program->id}";
        $this->get($uri, $this->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    
    public function test_showAll_200_returnProgramContainClientPartipantType()
    {
        $response = [
            "total" => 4,
            "list" => [
                [
                    "id" => $this->program->id,
                    "name" => $this->program->name,
                    "description" => $this->program->description,
                    "published" => $this->program->published,
                    "participantTypes" => explode(",", $this->program->participantTypes),
                    "removed" => $this->program->removed,
                    "illustration" => [
                        "id" => $this->firmFileInfo->id,
                        "url" => "/{$this->firmFileInfo->fileInfo->name}",
                    ],
                ],
                [
                    "id" => $this->programOne_forClientType->id,
                    "name" => $this->programOne_forClientType->name,
                    "description" => $this->programOne_forClientType->description,
                    "published" => $this->programOne_forClientType->published,
                    "participantTypes" => explode(",", $this->programOne_forClientType->participantTypes),
                    "removed" => $this->programOne_forClientType->removed,
                    "illustration" => [
                        "id" => $this->firmFileInfoOne->id,
                        "url" => "/{$this->firmFileInfoOne->fileInfo->name}",
                    ],
                ],
                [
                    "id" => $this->programTwo_forUserType->id,
                    "name" => $this->programTwo_forUserType->name,
                    "description" => $this->programTwo_forUserType->description,
                    "published" => $this->programTwo_forUserType->published,
                    "participantTypes" => explode(",", $this->programTwo_forUserType->participantTypes),
                    "removed" => $this->programTwo_forUserType->removed,
                    "illustration" => null,
                ],
                [
                    "id" => $this->programThree_forTeamType->id,
                    "name" => $this->programThree_forTeamType->name,
                    "description" => $this->programThree_forTeamType->description,
                    "published" => $this->programThree_forTeamType->published,
                    "participantTypes" => explode(",", $this->programThree_forTeamType->participantTypes),
                    "removed" => $this->programThree_forTeamType->removed,
                    "illustration" => [
                        "id" => $this->firmFileInfoThree->id,
                        "url" => "/{$this->firmFileInfoThree->fileInfo->name}",
                    ],
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
                    "description" => $this->program->description,
                    "published" => $this->program->published,
                    "participantTypes" => explode(",", $this->program->participantTypes),
                    "removed" => $this->program->removed,
                    "illustration" => [
                        "id" => $this->firmFileInfo->id,
                        "url" => "/{$this->firmFileInfo->fileInfo->name}",
                    ],
                ],
                [
                    "id" => $this->programThree_forTeamType->id,
                    "name" => $this->programThree_forTeamType->name,
                    "description" => $this->programThree_forTeamType->description,
                    "published" => $this->programThree_forTeamType->published,
                    "participantTypes" => explode(",", $this->programThree_forTeamType->participantTypes),
                    "removed" => $this->programThree_forTeamType->removed,
                    "illustration" => [
                        "id" => $this->firmFileInfoThree->id,
                        "url" => "/{$this->firmFileInfoThree->fileInfo->name}",
                    ],
                ],
            ],
        ];
        $uri = $this->programUri . "/team-types";
        $this->get($uri, $this->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
        
    }
}

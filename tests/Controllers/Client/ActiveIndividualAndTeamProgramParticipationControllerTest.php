<?php

namespace Tests\Controllers\Client;

use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfMember;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;

class ActiveIndividualAndTeamProgramParticipationControllerTest extends ClientTestCase
{
    protected $activeIndividualAndTeamProgramParticipantControllerUri;
    protected $clientParticipation_11;
    protected $clientParticipation_21;
    protected $teamParticipant_31;
    protected $teamParticipant_41;
    protected $member_11_team1;
    protected $member_21_team2;
    protected $otherClient;
    
    protected function setUp(): void
    {
        parent::setUp();
        $firm = $this->client->firm;
        $this->activeIndividualAndTeamProgramParticipantControllerUri = $this->clientUri . "/active-individual-and-team-program-participation";
        
        $this->connection->table('Program')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        
        $programOne = new RecordOfProgram($firm, '1');
        $programTwo = new RecordOfProgram($firm, '2');
        $programThree = new RecordOfProgram($firm, '3');
        $programFour = new RecordOfProgram($firm, '4');
        
        $participant_11_prog1 = new RecordOfParticipant($programOne, '11');
        $participant_21_prog2 = new RecordOfParticipant($programTwo, '21');
        $participant_31_prog3 = new RecordOfParticipant($programThree, '31');
        $participant_41_prog4 = new RecordOfParticipant($programFour, '41');
        
        $this->clientParticipation_11 = new RecordOfClientParticipant($this->client, $participant_11_prog1);
        $this->clientParticipation_21 = new RecordOfClientParticipant($this->client, $participant_21_prog2);
        
        $teamOne = new RecordOfTeam($firm, $this->client, '1');
        $teamTwo = new RecordOfTeam($firm, $this->client, '2');
        
        $this->member_11_team1 = new RecordOfMember($teamOne, $this->client, '11');
        $this->member_21_team2 = new RecordOfMember($teamTwo, $this->client, '21');
        
        $this->teamParticipant_31 = new RecordOfTeamProgramParticipation($teamOne, $participant_31_prog3);
        $this->teamParticipant_41 = new RecordOfTeamProgramParticipation($teamTwo, $participant_41_prog4);
        
        $this->otherClient = new RecordOfClient($firm, 'other');
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
    }
    
    protected function showAll()
    {
        $this->clientParticipation_11->participant->program->insert($this->connection);
        $this->clientParticipation_21->participant->program->insert($this->connection);
        $this->teamParticipant_31->participant->program->insert($this->connection);
        $this->teamParticipant_41->participant->program->insert($this->connection);
        
        $this->clientParticipation_11->participant->insert($this->connection);
        $this->clientParticipation_21->participant->insert($this->connection);
        $this->teamParticipant_31->participant->insert($this->connection);
        $this->teamParticipant_41->participant->insert($this->connection);
        
        $this->teamParticipant_31->team->insert($this->connection);
        $this->teamParticipant_41->team->insert($this->connection);
        
        $this->member_11_team1->insert($this->connection);
        $this->member_21_team2->insert($this->connection);
        
        $this->clientParticipation_11->insert($this->connection);
        $this->clientParticipation_21->insert($this->connection);
        $this->teamParticipant_31->insert($this->connection);
        $this->teamParticipant_41->insert($this->connection);
        
        $this->get($this->activeIndividualAndTeamProgramParticipantControllerUri, $this->client->token);
    }
    public function test_showAll_200()
    {
        $this->showAll();
        $this->seeStatusCode(200);
        
        $result = [
            'data' => [
                'list' => [
                    [
                        'id' => $this->clientParticipation_11->participant->id,
                        'active' => $this->clientParticipation_11->participant->active,
                        'enrolledTime' => $this->clientParticipation_11->participant->enrolledTime,
                        'program' => [
                            'id' => $this->clientParticipation_11->participant->program->id,
                            'name' => $this->clientParticipation_11->participant->program->name,
                            'description' => $this->clientParticipation_11->participant->program->description,
                            'strictMissionOrder' => $this->clientParticipation_11->participant->program->strictMissionOrder,
                            'published' => $this->clientParticipation_11->participant->program->published,
                            'removed' => $this->clientParticipation_11->participant->program->removed,
                        ],
                        'team' => null,
                    ],
                    [
                        'id' => $this->clientParticipation_21->participant->id,
                        'active' => $this->clientParticipation_21->participant->active,
                        'enrolledTime' => $this->clientParticipation_21->participant->enrolledTime,
                        'program' => [
                            'id' => $this->clientParticipation_21->participant->program->id,
                            'name' => $this->clientParticipation_21->participant->program->name,
                            'description' => $this->clientParticipation_21->participant->program->description,
                            'strictMissionOrder' => $this->clientParticipation_21->participant->program->strictMissionOrder,
                            'published' => $this->clientParticipation_21->participant->program->published,
                            'removed' => $this->clientParticipation_21->participant->program->removed,
                        ],
                        'team' => null,
                    ],
                    [
                        'id' => $this->teamParticipant_31->participant->id,
                        'active' => $this->teamParticipant_31->participant->active,
                        'enrolledTime' => $this->teamParticipant_31->participant->enrolledTime,
                        'program' => [
                            'id' => $this->teamParticipant_31->participant->program->id,
                            'name' => $this->teamParticipant_31->participant->program->name,
                            'description' => $this->teamParticipant_31->participant->program->description,
                            'strictMissionOrder' => $this->teamParticipant_31->participant->program->strictMissionOrder,
                            'published' => $this->teamParticipant_31->participant->program->published,
                            'removed' => $this->teamParticipant_31->participant->program->removed,
                        ],
                        'team' => [
                            'id' => $this->teamParticipant_31->team->id,
                            'name' => $this->teamParticipant_31->team->name,
                        ],
                    ],
                    [
                        'id' => $this->teamParticipant_41->participant->id,
                        'active' => $this->teamParticipant_41->participant->active,
                        'enrolledTime' => $this->teamParticipant_41->participant->enrolledTime,
                        'program' => [
                            'id' => $this->teamParticipant_41->participant->program->id,
                            'name' => $this->teamParticipant_41->participant->program->name,
                            'description' => $this->teamParticipant_41->participant->program->description,
                            'strictMissionOrder' => $this->teamParticipant_41->participant->program->strictMissionOrder,
                            'published' => $this->teamParticipant_41->participant->program->published,
                            'removed' => $this->teamParticipant_41->participant->program->removed,
                        ],
                        'team' => [
                            'id' => $this->teamParticipant_41->team->id,
                            'name' => $this->teamParticipant_41->team->name,
                        ],
                    ],
                ],
            ],
            'meta' => [
                'code' => 200,
                'type' => 'OK',
            ],
        ];
        $this->seeJsonContains($result);
    }
    public function test_showAll_containInactiveClientParticipant_excludeFromResult_200()
    {
        $this->clientParticipation_11->participant->active = false;
        $this->showAll();
        $this->seeStatusCode(200);
        
        $result = [
            'data' => [
                'list' => [
                    [
                        'id' => $this->clientParticipation_21->participant->id,
                        'active' => $this->clientParticipation_21->participant->active,
                        'enrolledTime' => $this->clientParticipation_21->participant->enrolledTime,
                        'program' => [
                            'id' => $this->clientParticipation_21->participant->program->id,
                            'name' => $this->clientParticipation_21->participant->program->name,
                            'description' => $this->clientParticipation_21->participant->program->description,
                            'strictMissionOrder' => $this->clientParticipation_21->participant->program->strictMissionOrder,
                            'published' => $this->clientParticipation_21->participant->program->published,
                            'removed' => $this->clientParticipation_21->participant->program->removed,
                        ],
                        'team' => null,
                    ],
                    [
                        'id' => $this->teamParticipant_31->participant->id,
                        'active' => $this->teamParticipant_31->participant->active,
                        'enrolledTime' => $this->teamParticipant_31->participant->enrolledTime,
                        'program' => [
                            'id' => $this->teamParticipant_31->participant->program->id,
                            'name' => $this->teamParticipant_31->participant->program->name,
                            'description' => $this->teamParticipant_31->participant->program->description,
                            'strictMissionOrder' => $this->teamParticipant_31->participant->program->strictMissionOrder,
                            'published' => $this->teamParticipant_31->participant->program->published,
                            'removed' => $this->teamParticipant_31->participant->program->removed,
                        ],
                        'team' => [
                            'id' => $this->teamParticipant_31->team->id,
                            'name' => $this->teamParticipant_31->team->name,
                        ],
                    ],
                    [
                        'id' => $this->teamParticipant_41->participant->id,
                        'active' => $this->teamParticipant_41->participant->active,
                        'enrolledTime' => $this->teamParticipant_41->participant->enrolledTime,
                        'program' => [
                            'id' => $this->teamParticipant_41->participant->program->id,
                            'name' => $this->teamParticipant_41->participant->program->name,
                            'description' => $this->teamParticipant_41->participant->program->description,
                            'strictMissionOrder' => $this->teamParticipant_41->participant->program->strictMissionOrder,
                            'published' => $this->teamParticipant_41->participant->program->published,
                            'removed' => $this->teamParticipant_41->participant->program->removed,
                        ],
                        'team' => [
                            'id' => $this->teamParticipant_41->team->id,
                            'name' => $this->teamParticipant_41->team->name,
                        ],
                    ],
                ],
            ],
            'meta' => [
                'code' => 200,
                'type' => 'OK',
            ],
        ];
        $this->seeJsonContains($result);
    }
    public function test_showAll_excludeClientParticipantBelongsToOtherClient()
    {
        $this->otherClient->insert($this->connection);
        $this->clientParticipation_21->client = $this->otherClient;
        $this->showAll();
        $this->seeStatusCode(200);
        
        $result = [
            'data' => [
                'list' => [
                    [
                        'id' => $this->clientParticipation_11->participant->id,
                        'active' => $this->clientParticipation_11->participant->active,
                        'enrolledTime' => $this->clientParticipation_11->participant->enrolledTime,
                        'program' => [
                            'id' => $this->clientParticipation_11->participant->program->id,
                            'name' => $this->clientParticipation_11->participant->program->name,
                            'description' => $this->clientParticipation_11->participant->program->description,
                            'strictMissionOrder' => $this->clientParticipation_11->participant->program->strictMissionOrder,
                            'published' => $this->clientParticipation_11->participant->program->published,
                            'removed' => $this->clientParticipation_11->participant->program->removed,
                        ],
                        'team' => null,
                    ],
                    [
                        'id' => $this->teamParticipant_31->participant->id,
                        'active' => $this->teamParticipant_31->participant->active,
                        'enrolledTime' => $this->teamParticipant_31->participant->enrolledTime,
                        'program' => [
                            'id' => $this->teamParticipant_31->participant->program->id,
                            'name' => $this->teamParticipant_31->participant->program->name,
                            'description' => $this->teamParticipant_31->participant->program->description,
                            'strictMissionOrder' => $this->teamParticipant_31->participant->program->strictMissionOrder,
                            'published' => $this->teamParticipant_31->participant->program->published,
                            'removed' => $this->teamParticipant_31->participant->program->removed,
                        ],
                        'team' => [
                            'id' => $this->teamParticipant_31->team->id,
                            'name' => $this->teamParticipant_31->team->name,
                        ],
                    ],
                    [
                        'id' => $this->teamParticipant_41->participant->id,
                        'active' => $this->teamParticipant_41->participant->active,
                        'enrolledTime' => $this->teamParticipant_41->participant->enrolledTime,
                        'program' => [
                            'id' => $this->teamParticipant_41->participant->program->id,
                            'name' => $this->teamParticipant_41->participant->program->name,
                            'description' => $this->teamParticipant_41->participant->program->description,
                            'strictMissionOrder' => $this->teamParticipant_41->participant->program->strictMissionOrder,
                            'published' => $this->teamParticipant_41->participant->program->published,
                            'removed' => $this->teamParticipant_41->participant->program->removed,
                        ],
                        'team' => [
                            'id' => $this->teamParticipant_41->team->id,
                            'name' => $this->teamParticipant_41->team->name,
                        ],
                    ],
                ],
            ],
            'meta' => [
                'code' => 200,
                'type' => 'OK',
            ],
        ];
        $this->seeJsonContains($result);
    }
    public function test_showAll_containInactiveTeamPartipant_excludeFormResult_200()
    {
        $this->teamParticipant_31->participant->active = false;
        $this->showAll();
        $this->seeStatusCode(200);
        
        $result = [
            'data' => [
                'list' => [
                    [
                        'id' => $this->clientParticipation_11->participant->id,
                        'active' => $this->clientParticipation_11->participant->active,
                        'enrolledTime' => $this->clientParticipation_11->participant->enrolledTime,
                        'program' => [
                            'id' => $this->clientParticipation_11->participant->program->id,
                            'name' => $this->clientParticipation_11->participant->program->name,
                            'description' => $this->clientParticipation_11->participant->program->description,
                            'strictMissionOrder' => $this->clientParticipation_11->participant->program->strictMissionOrder,
                            'published' => $this->clientParticipation_11->participant->program->published,
                            'removed' => $this->clientParticipation_11->participant->program->removed,
                        ],
                        'team' => null,
                    ],
                    [
                        'id' => $this->clientParticipation_21->participant->id,
                        'active' => $this->clientParticipation_21->participant->active,
                        'enrolledTime' => $this->clientParticipation_21->participant->enrolledTime,
                        'program' => [
                            'id' => $this->clientParticipation_21->participant->program->id,
                            'name' => $this->clientParticipation_21->participant->program->name,
                            'description' => $this->clientParticipation_21->participant->program->description,
                            'strictMissionOrder' => $this->clientParticipation_21->participant->program->strictMissionOrder,
                            'published' => $this->clientParticipation_21->participant->program->published,
                            'removed' => $this->clientParticipation_21->participant->program->removed,
                        ],
                        'team' => null,
                    ],
                    [
                        'id' => $this->teamParticipant_41->participant->id,
                        'active' => $this->teamParticipant_41->participant->active,
                        'enrolledTime' => $this->teamParticipant_41->participant->enrolledTime,
                        'program' => [
                            'id' => $this->teamParticipant_41->participant->program->id,
                            'name' => $this->teamParticipant_41->participant->program->name,
                            'description' => $this->teamParticipant_41->participant->program->description,
                            'strictMissionOrder' => $this->teamParticipant_41->participant->program->strictMissionOrder,
                            'published' => $this->teamParticipant_41->participant->program->published,
                            'removed' => $this->teamParticipant_41->participant->program->removed,
                        ],
                        'team' => [
                            'id' => $this->teamParticipant_41->team->id,
                            'name' => $this->teamParticipant_41->team->name,
                        ],
                    ],
                ],
            ],
            'meta' => [
                'code' => 200,
                'type' => 'OK',
            ],
        ];
        $this->seeJsonContains($result);
    }
    public function test_showAll_containInactiveTeamMember_excludeFormResult_200()
    {
        $this->member_11_team1->active = false;
        $this->showAll();
        $this->seeStatusCode(200);
        
        $result = [
            'data' => [
                'list' => [
                    [
                        'id' => $this->clientParticipation_11->participant->id,
                        'active' => $this->clientParticipation_11->participant->active,
                        'enrolledTime' => $this->clientParticipation_11->participant->enrolledTime,
                        'program' => [
                            'id' => $this->clientParticipation_11->participant->program->id,
                            'name' => $this->clientParticipation_11->participant->program->name,
                            'description' => $this->clientParticipation_11->participant->program->description,
                            'strictMissionOrder' => $this->clientParticipation_11->participant->program->strictMissionOrder,
                            'published' => $this->clientParticipation_11->participant->program->published,
                            'removed' => $this->clientParticipation_11->participant->program->removed,
                        ],
                        'team' => null,
                    ],
                    [
                        'id' => $this->clientParticipation_21->participant->id,
                        'active' => $this->clientParticipation_21->participant->active,
                        'enrolledTime' => $this->clientParticipation_21->participant->enrolledTime,
                        'program' => [
                            'id' => $this->clientParticipation_21->participant->program->id,
                            'name' => $this->clientParticipation_21->participant->program->name,
                            'description' => $this->clientParticipation_21->participant->program->description,
                            'strictMissionOrder' => $this->clientParticipation_21->participant->program->strictMissionOrder,
                            'published' => $this->clientParticipation_21->participant->program->published,
                            'removed' => $this->clientParticipation_21->participant->program->removed,
                        ],
                        'team' => null,
                    ],
                    [
                        'id' => $this->teamParticipant_41->participant->id,
                        'active' => $this->teamParticipant_41->participant->active,
                        'enrolledTime' => $this->teamParticipant_41->participant->enrolledTime,
                        'program' => [
                            'id' => $this->teamParticipant_41->participant->program->id,
                            'name' => $this->teamParticipant_41->participant->program->name,
                            'description' => $this->teamParticipant_41->participant->program->description,
                            'strictMissionOrder' => $this->teamParticipant_41->participant->program->strictMissionOrder,
                            'published' => $this->teamParticipant_41->participant->program->published,
                            'removed' => $this->teamParticipant_41->participant->program->removed,
                        ],
                        'team' => [
                            'id' => $this->teamParticipant_41->team->id,
                            'name' => $this->teamParticipant_41->team->name,
                        ],
                    ],
                ],
            ],
            'meta' => [
                'code' => 200,
                'type' => 'OK',
            ],
        ];
        $this->seeJsonContains($result);
    }
}

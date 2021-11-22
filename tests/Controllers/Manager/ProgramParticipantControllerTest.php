<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\Manager\EnhanceManagerTestCase;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfMember;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\RecordOfFirm;

class ProgramParticipantControllerTest extends EnhanceManagerTestCase
{

    protected $clientParticipant_11_p1c1;
    protected $teamParticipant_12_p1t2;
    protected $clientParticipant_21_p2c1;
    protected $clientOne;
    protected $teamTwo;
    protected $programOne;
    protected $programTwo;
    protected $teamMemberOne;
    protected $showAllUri;
    protected $addClientParticipantRequest;
    protected $addTeamParticipantRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();

        $this->showAllUri = $this->managerUri . "/participants";

        $firm = $this->managerOne->firm;

        $this->clientOne = new RecordOfClient($firm, '1');

        $this->programOne = new RecordOfProgram($firm, '1');
        $this->programTwo = new RecordOfProgram($firm, '2');

        $this->teamTwo = new RecordOfTeam($firm, $this->clientOne, '2');

        $this->teamMemberOne = new RecordOfMember($this->teamTwo, $this->clientOne, '11');

        $participant_11_p1 = new RecordOfParticipant($this->programOne, '11');
        $participant_21_p2 = new RecordOfParticipant($this->programTwo, '21');
        $participant_12_p1 = new RecordOfParticipant($this->programOne, '12');

        $this->clientParticipant_11_p1c1 = new RecordOfClientParticipant($this->clientOne, $participant_11_p1);
        $this->clientParticipant_21_p2c1 = new RecordOfClientParticipant($this->clientOne, $participant_21_p2);
        $this->teamParticipant_12_p1t2 = new RecordOfTeamProgramParticipation($this->teamTwo, $participant_12_p1);

        $this->addClientParticipantRequest = [
            'clientId' => $this->clientOne->id,
        ];

        $this->addTeamParticipantRequest = [
            'teamId' => $this->teamTwo->id,
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
    }

    protected function addClientParticipant()
    {
        $this->insertPreparedManagerRecord();
        
        $this->programOne->insert($this->connection);
        $this->clientOne->insert($this->connection);
        
        $uri = $this->managerUri . "/programs/{$this->programOne->id}/client-participants";
        $this->put($uri, $this->addClientParticipantRequest, $this->managerOne->token);
    }
    public function test_addClientParticipant_200()
    {
        $this->addClientParticipant();
        $this->seeStatusCode(200);
        
        $response = [
            "active" => true,
            "note" => null,
            "client" => [
                'id' => $this->clientOne->id,
                'name' => $this->clientOne->getFullName(),
            ],
            "user" => null,
            "team" => null,
        ];
        $this->seeJsonContains($response);
        
        $clientParticipantRecord = [
            'Client_id' => $this->clientOne->id,
        ];
        $this->seeInDatabase('ClientParticipant', $clientParticipantRecord);
        
        $participantRecord = [
            'Program_id' => $this->programOne->id,
            'active' => true,
            'note' => null,
        ];
        $this->seeInDatabase('Participant', $participantRecord);
    }
    public function test_addClientParticipant_alreadyActiveParticipant_403()
    {
        $this->clientParticipant_11_p1c1->insert($this->connection);
        
        $this->addClientParticipant();
        $this->seeStatusCode(403);
    }
    public function test_addClientParticipant_formerParticipant_renewParticipation()
    {
        $this->clientParticipant_11_p1c1->participant->active = false;
        $this->clientParticipant_11_p1c1->insert($this->connection);
        
        $this->addClientParticipant();
        $this->seeStatusCode(200);
        
        $participantRecord = [
            'id' => $this->clientParticipant_11_p1c1->participant->id,
            'active' => true,
        ];
        $this->seeInDatabase('Participant', $participantRecord);
    }
    public function test_addClientParticipant_unuseableClient_belongsToOtherFirm()
    {
        $firm = new RecordOfFirm('zzz');
        $firm->insert($this->connection);
        
        $this->clientOne->firm = $firm;
        
        $this->addClientParticipant();
        $this->seeStatusCode(403);
    }
    public function test_addClientParticipant_unuseableClient_inactive_403()
    {
        $this->clientOne->activated = false;
        
        $this->addClientParticipant();
        $this->seeStatusCode(403);
    }
    public function test_addClientParticipant_unuseableProgram_belongsToOtherFirm_403()
    {
        $firm = new RecordOfFirm('zzz');
        $firm->insert($this->connection);
        
        $this->programOne->firm = $firm;
        
        $this->addClientParticipant();
        $this->seeStatusCode(403);
    }
    public function test_addClientParticipant_unuseableProgram_unpublished_403()
    {
        $this->programOne->published = false;
        
        $this->addClientParticipant();
        $this->seeStatusCode(403);
    }
    public function test_addClientParticipant_unuseableProgram_removed_404()
    {
        $this->programOne->removed = true;
        
        $this->addClientParticipant();
        $this->seeStatusCode(404);
    }
    public function test_addClientParticipant_clienTypeNotSupportedInProgram_403()
    {
        $this->programOne->participantTypes = 'team';
        $this->addClientParticipant();
        $this->seeStatusCode(403);
    }

    protected function addTeamParticipant()
    {
        $this->insertPreparedManagerRecord();
        
        $this->programOne->insert($this->connection);
        $this->teamTwo->insert($this->connection);
        $this->teamMemberOne->client->insert($this->connection);
        $this->teamMemberOne->insert($this->connection);
        
        $uri = $this->managerUri . "/programs/{$this->programOne->id}/team-participants";
        $this->put($uri, $this->addTeamParticipantRequest, $this->managerOne->token);
    }
    public function test_addTeamParticipant_200()
    {
        $this->addTeamParticipant();
        $this->seeStatusCode(200);
        
        $response = [
            "active" => true,
            "note" => null,
            "team" => [
                'id' => $this->teamTwo->id,
                'name' => $this->teamTwo->name,
                'members' => [
                    [
                        'id' => $this->teamMemberOne->id,
                        'client' => [
                            'id' => $this->teamMemberOne->client->id,
                            'name' => $this->teamMemberOne->client->getFullName(),
                        ],
                    ],
                ],
            ],
            "user" => null,
            "client" => null,
        ];
        $this->seeJsonContains($response);
        
        $teamParticipantRecord = [
            'Team_id' => $this->teamTwo->id,
        ];
        $this->seeInDatabase('TeamParticipant', $teamParticipantRecord);
        
        $participantRecord = [
            'Program_id' => $this->programOne->id,
            'active' => true,
            'note' => null,
        ];
        $this->seeInDatabase('Participant', $participantRecord);
    }
    public function test_addTeamParticipant_alreadyActiveParticipant_403()
    {
        $this->teamParticipant_12_p1t2->insert($this->connection);
        
        $this->addTeamParticipant();
        $this->seeStatusCode(403);
    }
    public function test_addTeamParticipant_formerParticipant_renewParticipation()
    {
        $this->teamParticipant_12_p1t2->participant->active = false;
        $this->teamParticipant_12_p1t2->insert($this->connection);
        
        $this->addTeamParticipant();
        $this->seeStatusCode(200);
        
        $participantRecord = [
            'id' => $this->teamParticipant_12_p1t2->participant->id,
            'active' => true,
        ];
        $this->seeInDatabase('Participant', $participantRecord);
    }
    public function test_addTeamParticipant_unuseableTeam_belongsToOtherFirm()
    {
        $firm = new RecordOfFirm('zzz');
        $firm->insert($this->connection);
        
        $this->teamTwo->firm = $firm;
        
        $this->addTeamParticipant();
        $this->seeStatusCode(403);
    }
    public function test_addTeamParticipant_unuseableProgram_belongsToOtherFirm_403()
    {
        $firm = new RecordOfFirm('zzz');
        $firm->insert($this->connection);
        
        $this->programOne->firm = $firm;
        
        $this->addTeamParticipant();
        $this->seeStatusCode(403);
    }
    public function test_addTeamParticipant_unuseableProgram_unpublished_403()
    {
        $this->programOne->published = false;
        
        $this->addTeamParticipant();
        $this->seeStatusCode(403);
    }
    public function test_addTeamParticipant_unuseableProgram_removed_404()
    {
        $this->programOne->removed = true;
        
        $this->addTeamParticipant();
        $this->seeStatusCode(404);
    }
    public function test_addTeamParticipant_clienTypeNotSupportedInProgram_403()
    {
        $this->programOne->participantTypes = 'client';
        $this->addTeamParticipant();
        $this->seeStatusCode(403);
    }
    
    protected function show()
    {
        $this->insertPreparedManagerRecord();
        
        $this->clientParticipant_11_p1c1->participant->program->insert($this->connection);
        $this->clientParticipant_11_p1c1->client->insert($this->connection);
        $this->clientParticipant_11_p1c1->insert($this->connection);
        
        $uri = $this->managerUri . "/participants/{$this->clientParticipant_11_p1c1->id}";
        $this->get($uri, $this->managerOne->token);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->clientParticipant_11_p1c1->id,
            'enrolledTime' => $this->clientParticipant_11_p1c1->participant->enrolledTime,
            'active' => $this->clientParticipant_11_p1c1->participant->active,
            'note' => $this->clientParticipant_11_p1c1->participant->note,
            "client" => [
                'id' => $this->clientParticipant_11_p1c1->client->id,
                'name' => $this->clientParticipant_11_p1c1->client->getFullName(),
            ],
            "user" => null,
            "team" => null,
        ];
        $this->seeJsonContains($response);
    }
    
    protected function showAll()
    {
        $this->insertPreparedManagerRecord();
        
        $this->teamMemberOne->insert($this->connection);
        
        $this->clientParticipant_11_p1c1->client->insert($this->connection);
        $this->teamParticipant_12_p1t2->team->insert($this->connection);
        
        $this->clientParticipant_11_p1c1->participant->program->insert($this->connection);
        $this->clientParticipant_21_p2c1->participant->program->insert($this->connection);
        
        $this->clientParticipant_11_p1c1->insert($this->connection);
        $this->clientParticipant_21_p2c1->insert($this->connection);
        $this->teamParticipant_12_p1t2->insert($this->connection);
        
        $this->get($this->showAllUri, $this->managerOne->token);
    }
    public function test_showAll_200()
    {
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 3,
            'list' => [
                [
                    'id' => $this->clientParticipant_11_p1c1->id,
                    'enrolledTime' => $this->clientParticipant_11_p1c1->participant->enrolledTime,
                    'active' => $this->clientParticipant_11_p1c1->participant->active,
                    'note' => $this->clientParticipant_11_p1c1->participant->note,
                    "client" => [
                        'id' => $this->clientParticipant_11_p1c1->client->id,
                        'name' => $this->clientParticipant_11_p1c1->client->getFullName(),
                    ],
                    "user" => null,
                    "team" => null,
                ],
                [
                    'id' => $this->clientParticipant_21_p2c1->id,
                    'enrolledTime' => $this->clientParticipant_21_p2c1->participant->enrolledTime,
                    'active' => $this->clientParticipant_21_p2c1->participant->active,
                    'note' => $this->clientParticipant_21_p2c1->participant->note,
                    "client" => [
                        'id' => $this->clientParticipant_21_p2c1->client->id,
                        'name' => $this->clientParticipant_21_p2c1->client->getFullName(),
                    ],
                    "user" => null,
                    "team" => null,
                ],
                [
                    'id' => $this->teamParticipant_12_p1t2->id,
                    'enrolledTime' => $this->teamParticipant_12_p1t2->participant->enrolledTime,
                    'active' => $this->teamParticipant_12_p1t2->participant->active,
                    'note' => $this->teamParticipant_12_p1t2->participant->note,
                    "client" => null,
                    "user" => null,
                    "team" => [
                        'id' => $this->teamParticipant_12_p1t2->team->id,
                        'name' => $this->teamParticipant_12_p1t2->team->name,
                        'members' => [
                            [
                                'id' => $this->teamMemberOne->id,
                                'client' => [
                                    'id' => $this->teamMemberOne->client->id,
                                    'name' => $this->teamMemberOne->client->getFullName(),
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_usingProgramIdListFilter_200()
    {
        $this->showAllUri .= "?programIdList[]={$this->programOne->id}";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 2,
            'list' => [
                [
                    'id' => $this->clientParticipant_11_p1c1->id,
                    'enrolledTime' => $this->clientParticipant_11_p1c1->participant->enrolledTime,
                    'active' => $this->clientParticipant_11_p1c1->participant->active,
                    'note' => $this->clientParticipant_11_p1c1->participant->note,
                    "client" => [
                        'id' => $this->clientParticipant_11_p1c1->client->id,
                        'name' => $this->clientParticipant_11_p1c1->client->getFullName(),
                    ],
                    "user" => null,
                    "team" => null,
                ],
                [
                    'id' => $this->teamParticipant_12_p1t2->id,
                    'enrolledTime' => $this->teamParticipant_12_p1t2->participant->enrolledTime,
                    'active' => $this->teamParticipant_12_p1t2->participant->active,
                    'note' => $this->teamParticipant_12_p1t2->participant->note,
                    "client" => null,
                    "user" => null,
                    "team" => [
                        'id' => $this->teamParticipant_12_p1t2->team->id,
                        'name' => $this->teamParticipant_12_p1t2->team->name,
                        'members' => [
                            [
                                'id' => $this->teamMemberOne->id,
                                'client' => [
                                    'id' => $this->teamMemberOne->client->id,
                                    'name' => $this->teamMemberOne->client->getFullName(),
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_usingActiveStatus_200()
    {
        $this->clientParticipant_21_p2c1->participant->active = false;
        $this->showAllUri .= "?activeStatus=true";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 2,
            'list' => [
                [
                    'id' => $this->clientParticipant_11_p1c1->id,
                    'enrolledTime' => $this->clientParticipant_11_p1c1->participant->enrolledTime,
                    'active' => $this->clientParticipant_11_p1c1->participant->active,
                    'note' => $this->clientParticipant_11_p1c1->participant->note,
                    "client" => [
                        'id' => $this->clientParticipant_11_p1c1->client->id,
                        'name' => $this->clientParticipant_11_p1c1->client->getFullName(),
                    ],
                    "user" => null,
                    "team" => null,
                ],
                [
                    'id' => $this->teamParticipant_12_p1t2->id,
                    'enrolledTime' => $this->teamParticipant_12_p1t2->participant->enrolledTime,
                    'active' => $this->teamParticipant_12_p1t2->participant->active,
                    'note' => $this->teamParticipant_12_p1t2->participant->note,
                    "client" => null,
                    "user" => null,
                    "team" => [
                        'id' => $this->teamParticipant_12_p1t2->team->id,
                        'name' => $this->teamParticipant_12_p1t2->team->name,
                        'members' => [
                            [
                                'id' => $this->teamMemberOne->id,
                                'client' => [
                                    'id' => $this->teamMemberOne->client->id,
                                    'name' => $this->teamMemberOne->client->getFullName(),
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }

}

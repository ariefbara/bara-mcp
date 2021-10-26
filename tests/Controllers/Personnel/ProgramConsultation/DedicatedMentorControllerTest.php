<?php

namespace Tests\Controllers\Personnel\ProgramConsultation;

use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfDedicatedMentor;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfMember;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;

class DedicatedMentorControllerTest extends ProgramConsultationTestCase
{
    protected $dedicatedMentorUri;
    protected $dedicatedMentorOne;
    protected $dedicatedMentorTwo;
    protected $clientParticipantOne;
    protected $teamParticipantTwo;
    protected $memberOne;
    protected $memberTwo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dedicatedMentorUri = $this->programConsultationUri . "/dedicated-mentors";
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('DedicatedMentor')->truncate();
        
        $program = $this->programConsultation->program;
        $firm = $program->firm;
        
        $participantOne = new RecordOfParticipant($program, '1');
        $participantTwo = new RecordOfParticipant($program, '2');
        
        $clientOne = new RecordOfClient($firm, '1');
        $clientTwo = new RecordOfClient($firm, '2');
        
        $teamOne = new RecordOfTeam($firm, $clientTwo, '1');
        
        $this->memberOne = new RecordOfMember($teamOne, $clientOne, '1');
        $this->memberTwo = new RecordOfMember($teamOne, $clientTwo, '2');
        
        $this->clientParticipantOne = new RecordOfClientParticipant($clientOne, $participantOne);
        $this->teamParticipantTwo = new RecordOfTeamProgramParticipation($teamOne, $participantTwo);
        
        $this->dedicatedMentorOne = new RecordOfDedicatedMentor($participantOne, $this->programConsultation, '1');
        $this->dedicatedMentorTwo = new RecordOfDedicatedMentor($participantTwo, $this->programConsultation, '2');
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        $this->connection->table('DedicatedMentor')->truncate();
    }
    
    protected function executeShow()
    {
        $this->teamParticipantTwo->team->insert($this->connection);
        $this->teamParticipantTwo->insert($this->connection);
        
        $this->memberOne->client->insert($this->connection);
        $this->memberTwo->client->insert($this->connection);
        
        $this->memberOne->insert($this->connection);
        $this->memberTwo->insert($this->connection);
        
        $this->dedicatedMentorTwo->insert($this->connection);
        
        $uri = $this->dedicatedMentorUri . "/{$this->dedicatedMentorTwo->id}";
        $this->get($uri, $this->programConsultation->personnel->token);
echo $uri;
    }
    public function test_show_200()
    {
        $this->executeShow();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->dedicatedMentorTwo->id,
            'modifiedTime' => $this->dedicatedMentorTwo->modifiedTime,
            'cancelled' => $this->dedicatedMentorTwo->cancelled,
            'participant' => [
                'id' => $this->dedicatedMentorTwo->participant->id,
                'client' => null,
                'team' => [
                    'id' => $this->teamParticipantTwo->team->id,
                    'name' => $this->teamParticipantTwo->team->name,
                    'members' => [
                        [
                            'id' => $this->memberOne->id,
                            'client' => [
                                'id' => $this->memberOne->client->id,
                                'name' => $this->memberOne->client->getFullName(),
                            ],
                        ],
                        [
                            'id' => $this->memberTwo->id,
                            'client' => [
                                'id' => $this->memberTwo->client->id,
                                'name' => $this->memberTwo->client->getFullName(),
                            ],
                        ],
                    ],
                ],
                'user' => null,
            ],
        ];
        $this->seeJsonContains($response);
    }
    
    
    protected function executeShowAll(?string $uri = null)
    {
        $this->teamParticipantTwo->team->insert($this->connection);
        
        $this->memberOne->client->insert($this->connection);
        $this->memberTwo->client->insert($this->connection);
        
        $this->memberOne->insert($this->connection);
        $this->memberTwo->insert($this->connection);
        
        $this->clientParticipantOne->insert($this->connection);
        $this->teamParticipantTwo->insert($this->connection);
        
        $this->dedicatedMentorOne->insert($this->connection);
        
        $this->dedicatedMentorTwo->insert($this->connection);
        
        $uri = $uri ?? $this->dedicatedMentorUri;
        $this->get($uri, $this->programConsultation->personnel->token);
    }
    public function test_showAll_200()
    {
        $this->executeShowAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 2];
        $this->seeJsonContains($totalResponse);
        
        $dedicatedMentorOneResponse = [
            'id' => $this->dedicatedMentorOne->id,
            'modifiedTime' => $this->dedicatedMentorOne->modifiedTime,
            'cancelled' => $this->dedicatedMentorOne->cancelled,
            'participant' => [
                'id' => $this->dedicatedMentorOne->participant->id,
                'name' => $this->clientParticipantOne->client->getFullName(),
            ],
        ];
        $this->seeJsonContains($dedicatedMentorOneResponse);
        
        $dedicatedMentorTwoResponse = [
            'id' => $this->dedicatedMentorTwo->id,
            'modifiedTime' => $this->dedicatedMentorTwo->modifiedTime,
            'cancelled' => $this->dedicatedMentorTwo->cancelled,
            'participant' => [
                'id' => $this->dedicatedMentorTwo->participant->id,
                'name' => $this->teamParticipantTwo->team->name,
            ],
        ];
        $this->seeJsonContains($dedicatedMentorTwoResponse);
    }
    public function test_showAll_userCancelledStatusFilter()
    {
        $this->dedicatedMentorTwo->cancelled = true;
        $uri = $this->dedicatedMentorUri . "?cancelledStatus=false";
        $this->executeShowAll($uri);
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        
        $dedicatedMentorOneResponse = [
            'id' => $this->dedicatedMentorOne->id,
            'modifiedTime' => $this->dedicatedMentorOne->modifiedTime,
            'cancelled' => $this->dedicatedMentorOne->cancelled,
            'participant' => [
                'id' => $this->dedicatedMentorOne->participant->id,
                'name' => $this->clientParticipantOne->client->getFullName(),
            ],
        ];
        $this->seeJsonContains($dedicatedMentorOneResponse);
    }
}

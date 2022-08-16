<?php

namespace Tests\Controllers\Personnel;

use SharedContext\Domain\ValueObject\ParticipantStatus;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\RecordOfUser;
use Tests\Controllers\RecordPreparation\User\RecordOfUserParticipant;

class ParticipantControllerTest extends PersonnelTestCase
{

    protected $coordinatorOne_p1;
    protected $coordinatorTwo_p2;
    protected $clientParticipantOne_p1;
    protected $clientParticipantTwo_p2;
    protected $teamParticipantOne_p1;
    protected $teamParticipantTwo_p2;
    protected $userParticipantOne_p1;
    protected $userParticipantTwo_p2;
    //
    protected $participantUri;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Coordinator')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('UserParticipant')->truncate();

        $firm = $this->personnel->firm;
        
        $programOne = new RecordOfProgram($firm, '1');
        $programTwo = new RecordOfProgram($firm, '2');
        
        $this->coordinatorOne_p1 = new RecordOfCoordinator($programOne, $this->personnel, '1');
        $this->coordinatorTwo_p2= new RecordOfCoordinator($programTwo, $this->personnel, '2');
        
        $participantOne_client_p1 = new RecordOfParticipant($programOne, '1');
        $participantTwo_client_p2 = new RecordOfParticipant($programTwo, '2');
        $participantThree_team_p1 = new RecordOfParticipant($programOne, '3');
        $participantFour_team_p2 = new RecordOfParticipant($programTwo, '4');
        $participantFive_user_p1 = new RecordOfParticipant($programOne, '5');
        $participantSix_user_p2 = new RecordOfParticipant($programTwo, '6');
        
        $clientOne = new RecordOfClient($firm, '1');
        $clientTwo = new RecordOfClient($firm, '2');
        
        $this->clientParticipantOne_p1 = new RecordOfClientParticipant($clientOne, $participantOne_client_p1);
        $this->clientParticipantTwo_p2 = new RecordOfClientParticipant($clientTwo, $participantTwo_client_p2);
        
        $teamOne = new RecordOfTeam($firm, $clientOne, '1');
        $teamTwo = new RecordOfTeam($firm, $clientOne, '2');
        
        $this->teamParticipantOne_p1 = new RecordOfTeamProgramParticipation($teamOne, $participantThree_team_p1);
        $this->teamParticipantTwo_p2 = new RecordOfTeamProgramParticipation($teamTwo, $participantFour_team_p2);
        
        $userOne = new RecordOfUser('1');
        $userTwo = new RecordOfUser('2');
        
        $this->userParticipantOne_p1 = new RecordOfUserParticipant($userOne, $participantFive_user_p1);
        $this->userParticipantTwo_p2 = new RecordOfUserParticipant($userTwo, $participantSix_user_p2);
        
        $this->participantUri = $this->personnelUri . "/participants";
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Coordinator')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('UserParticipant')->truncate();
    }
    
    protected function showAll()
    {
        $this->coordinatorOne_p1->program->insert($this->connection);
        $this->coordinatorTwo_p2->program->insert($this->connection);
        
        $this->coordinatorOne_p1->insert($this->connection);
        $this->coordinatorTwo_p2->insert($this->connection);
        
        $this->clientParticipantOne_p1->client->insert($this->connection);
        $this->clientParticipantOne_p1->insert($this->connection);
        
        $this->clientParticipantTwo_p2->client->insert($this->connection);
        $this->clientParticipantTwo_p2->insert($this->connection);
        
        $this->teamParticipantOne_p1->team->insert($this->connection);
        $this->teamParticipantOne_p1->insert($this->connection);
        
        $this->teamParticipantTwo_p2->team->insert($this->connection);
        $this->teamParticipantTwo_p2->insert($this->connection);
        
        $this->userParticipantOne_p1->user->insert($this->connection);
        $this->userParticipantOne_p1->insert($this->connection);
        
        $this->userParticipantTwo_p2->user->insert($this->connection);
        $this->userParticipantTwo_p2->insert($this->connection);
        
        $this->get($this->participantUri, $this->personnel->token);
    }
    public function test_showAll_200()
    {
$this->disableExceptionHandling();
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 6,
            'list' => [
                [
                    'id' => $this->clientParticipantOne_p1->participant->id,
                    'status' => 'REGISTERED',
                    'programPriceSnapshot' => $this->clientParticipantOne_p1->participant->programPrice,
                    'user' => null,
                    'team' => null,
                    'client' => [
                        'id' => $this->clientParticipantOne_p1->client->id,
                        'name' => $this->clientParticipantOne_p1->client->getFullName(),
                    ],
                    'program' => [
                        'id' => $this->clientParticipantOne_p1->participant->program->id,
                        'name' => $this->clientParticipantOne_p1->participant->program->name,
                        'price' => $this->clientParticipantOne_p1->participant->program->price,
                    ],
                ],
                [
                    'id' => $this->clientParticipantTwo_p2->participant->id,
                    'status' => 'REGISTERED',
                    'programPriceSnapshot' => $this->clientParticipantTwo_p2->participant->programPrice,
                    'user' => null,
                    'team' => null,
                    'client' => [
                        'id' => $this->clientParticipantTwo_p2->client->id,
                        'name' => $this->clientParticipantTwo_p2->client->getFullName(),
                    ],
                    'program' => [
                        'id' => $this->clientParticipantTwo_p2->participant->program->id,
                        'name' => $this->clientParticipantTwo_p2->participant->program->name,
                        'price' => $this->clientParticipantTwo_p2->participant->program->price,
                    ],
                ],
                [
                    'id' => $this->teamParticipantOne_p1->participant->id,
                    'status' => 'REGISTERED',
                    'programPriceSnapshot' => $this->teamParticipantOne_p1->participant->programPrice,
                    'user' => null,
                    'team' => [
                        'id' => $this->teamParticipantOne_p1->team->id,
                        'name' => $this->teamParticipantOne_p1->team->name,
                    ],
                    'client' => null,
                    'program' => [
                        'id' => $this->teamParticipantOne_p1->participant->program->id,
                        'name' => $this->teamParticipantOne_p1->participant->program->name,
                        'price' => $this->teamParticipantOne_p1->participant->program->price,
                    ],
                ],
                [
                    'id' => $this->teamParticipantTwo_p2->participant->id,
                    'status' => 'REGISTERED',
                    'programPriceSnapshot' => $this->teamParticipantTwo_p2->participant->programPrice,
                    'user' => null,
                    'team' => [
                        'id' => $this->teamParticipantTwo_p2->team->id,
                        'name' => $this->teamParticipantTwo_p2->team->name,
                    ],
                    'client' => null,
                    'program' => [
                        'id' => $this->teamParticipantTwo_p2->participant->program->id,
                        'name' => $this->teamParticipantTwo_p2->participant->program->name,
                        'price' => $this->teamParticipantTwo_p2->participant->program->price,
                    ],
                ],
                [
                    'id' => $this->userParticipantOne_p1->participant->id,
                    'status' => 'REGISTERED',
                    'programPriceSnapshot' => $this->userParticipantOne_p1->participant->programPrice,
                    'team' => null,
                    'client' => null,
                    'user' => [
                        'id' => $this->userParticipantOne_p1->user->id,
                        'name' => $this->userParticipantOne_p1->user->getFullName(),
                    ],
                    'program' => [
                        'id' => $this->userParticipantOne_p1->participant->program->id,
                        'name' => $this->userParticipantOne_p1->participant->program->name,
                        'price' => $this->userParticipantOne_p1->participant->program->price,
                    ],
                ],
                [
                    'id' => $this->userParticipantTwo_p2->participant->id,
                    'status' => 'REGISTERED',
                    'programPriceSnapshot' => $this->userParticipantTwo_p2->participant->programPrice,
                    'client' => null,
                    'team' => null,
                    'user' => [
                        'id' => $this->userParticipantTwo_p2->user->id,
                        'name' => $this->userParticipantTwo_p2->user->getFullName(),
                    ],
                    'program' => [
                        'id' => $this->userParticipantTwo_p2->participant->program->id,
                        'name' => $this->userParticipantTwo_p2->participant->program->name,
                        'price' => $this->userParticipantTwo_p2->participant->program->price,
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_excludeParticipantOfProgramNotCoordinatedByPersonnel()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        $participantOfOtherProgram = new RecordOfParticipant($otherProgram, 'other');
        $participantOfOtherProgram->insert($this->connection);
        
        $this->showAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 6]);
        $this->seeJsonDoesntContains(['id' => $participantOfOtherProgram->id]);
    }
    public function test_showAll_excludePartiicpantOfProgramWherePersonnelIsInactiveCoordinator_200()
    {
        $this->coordinatorOne_p1->active = false;
        $this->showAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 3]);
        $this->seeJsonDoesntContains(['id' => $this->clientParticipantOne_p1->id]);
        $this->seeJsonContains(['id' => $this->clientParticipantTwo_p2->id]);
        $this->seeJsonDoesntContains(['id' => $this->teamParticipantOne_p1->id]);
        $this->seeJsonContains(['id' => $this->teamParticipantTwo_p2->id]);
        $this->seeJsonDoesntContains(['id' => $this->userParticipantOne_p1->id]);
        $this->seeJsonContains(['id' => $this->userParticipantTwo_p2->id]);
    }
    public function test_showAll_useStatusFilter_200()
    {
        $this->clientParticipantOne_p1->participant->status = ParticipantStatus::ACTIVE;
        $this->clientParticipantTwo_p2->participant->status = ParticipantStatus::REGISTERED;
        $this->teamParticipantOne_p1->participant->status = ParticipantStatus::SETTLEMENT_REQUIRED;
        $this->teamParticipantTwo_p2->participant->status = ParticipantStatus::CANCELLED;
        $this->userParticipantOne_p1->participant->status = ParticipantStatus::REJECTED;
        $this->userParticipantTwo_p2->participant->status = ParticipantStatus::COMPLETED;
        
        $this->participantUri .= "?status[]=1&status[]=2&status[]=3";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 3]);
        $this->seeJsonContains(['id' => $this->clientParticipantOne_p1->id]);
        $this->seeJsonContains(['id' => $this->clientParticipantTwo_p2->id]);
        $this->seeJsonContains(['id' => $this->teamParticipantOne_p1->id]);
        $this->seeJsonDoesntContains(['id' => $this->teamParticipantTwo_p2->id]);
        $this->seeJsonDoesntContains(['id' => $this->userParticipantOne_p1->id]);
        $this->seeJsonDoesntContains(['id' => $this->userParticipantTwo_p2->id]);
    }

}
 
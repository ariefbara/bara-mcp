<?php

namespace Tests\Controllers\Personnel;

use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientRegistrant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfRegistrant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramRegistration;
use Tests\Controllers\RecordPreparation\RecordOfUser;
use Tests\Controllers\RecordPreparation\User\RecordOfUserRegistrant;

class ManageableNewApplicantControllerTest extends AggregatedCoordinatorInPersonnelContextTestCase
{
    protected $clientRegistrantOne_p1;
    protected $teamRegistrantTwo_p2;
    protected $userRegistrantThree_p1;
    protected $viewAllUri;

    protected function setUp(): void
    {
        parent::setUp();
        $this->viewAllUri = $this->personnelUri . "/manageable-new-applicants";
        
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('ClientRegistrant')->truncate();
        $this->connection->table('TeamRegistrant')->truncate();
        $this->connection->table('UserRegistrant')->truncate();
        
        $firm = $this->personnel->firm;
        
        $programOne = $this->coordinatorOne->program;
        $programTwo = $this->coordinatorTwo->program;
        
        $clientOne = new RecordOfClient($firm, 1);
        
        $teamOne = new RecordOfTeam($firm, $clientOne, 1);
        
        $userOne = new RecordOfUser(1);
        
        $registrantOne = new RecordOfRegistrant($programOne, 1);
        $registrantTwo = new RecordOfRegistrant($programTwo, 2);
        $registrantThree = new RecordOfRegistrant($programOne, 3);
        
        $this->clientRegistrantOne_p1 = new RecordOfClientRegistrant($clientOne, $registrantOne);
        $this->teamRegistrantTwo_p2 = new RecordOfTeamProgramRegistration($teamOne, $registrantTwo);
        $this->userRegistrantThree_p1 = new RecordOfUserRegistrant($userOne, $registrantThree);
    }
    protected function tearDown(): void
    {
//        parent::tearDown();
//        $this->connection->table('Client')->truncate();
//        $this->connection->table('Team')->truncate();
//        $this->connection->table('User')->truncate();
//        $this->connection->table('Registrant')->truncate();
//        $this->connection->table('ClientRegistrant')->truncate();
//        $this->connection->table('TeamRegistrant')->truncate();
//        $this->connection->table('UserRegistrant')->truncate();
    }
    
    protected function viewAll()
    {
        $this->persistAggregatedCoordinatorDependency();
        
        $this->clientRegistrantOne_p1->client->insert($this->connection);
        $this->teamRegistrantTwo_p2->team->insert($this->connection);
        $this->userRegistrantThree_p1->user->insert($this->connection);
        
        $this->clientRegistrantOne_p1->insert($this->connection);
        $this->teamRegistrantTwo_p2->insert($this->connection);
        $this->userRegistrantThree_p1->insert($this->connection);
        
        $this->get($this->viewAllUri, $this->personnel->token);
    }
    public function test_viewAll_200()
    {
$this->disableExceptionHandling();
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '3',
            'list' => [
                [
                    'id' => $this->clientRegistrantOne_p1->id,
                    'name' => $this->clientRegistrantOne_p1->client->getFullName(),
                    'registeredTime' => $this->clientRegistrantOne_p1->registrant->registeredTime,
                    'coordinatorId' => $this->coordinatorOne->id,
                    'programId' => $this->clientRegistrantOne_p1->registrant->program->id,
                    'programName' => $this->clientRegistrantOne_p1->registrant->program->name,
                ],
                [
                    'id' => $this->teamRegistrantTwo_p2->id,
                    'name' => $this->teamRegistrantTwo_p2->team->name,
                    'registeredTime' => $this->teamRegistrantTwo_p2->registrant->registeredTime,
                    'coordinatorId' => $this->coordinatorTwo->id,
                    'programId' => $this->teamRegistrantTwo_p2->registrant->program->id,
                    'programName' => $this->teamRegistrantTwo_p2->registrant->program->name,
                ],
                [
                    'id' => $this->userRegistrantThree_p1->id,
                    'name' => $this->userRegistrantThree_p1->user->getFullName(),
                    'registeredTime' => $this->userRegistrantThree_p1->registrant->registeredTime,
                    'coordinatorId' => $this->coordinatorOne->id,
                    'programId' => $this->userRegistrantThree_p1->registrant->program->id,
                    'programName' => $this->userRegistrantThree_p1->registrant->program->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewAll_excludeApplicantToUnmanagedProgram_noCoordinator()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        $this->teamRegistrantTwo_p2->registrant->program = $otherProgram;
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['id' => $this->clientRegistrantOne_p1->id]);
        $this->seeJsonDoesntContains(['id' => $this->teamRegistrantTwo_p2->id]);
        $this->seeJsonContains(['id' => $this->userRegistrantThree_p1->id]);
    }
    public function test_viewAll_excludeApplicantToUnmanagedProgram_inactiveCoordinator()
    {
        $this->coordinatorTwo->active = false;
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['id' => $this->clientRegistrantOne_p1->id]);
        $this->seeJsonDoesntContains(['id' => $this->teamRegistrantTwo_p2->id]);
        $this->seeJsonContains(['id' => $this->userRegistrantThree_p1->id]);
    }
    public function test_viewAll_paginationSet_returnByRegisteredTimeAscOrder()
    {
        $this->teamRegistrantTwo_p2->registrant->registeredTime = (new \DateTime('-72 hour'));
        $this->clientRegistrantOne_p1->registrant->registeredTime = (new \DateTime('-48 hour'));
        $this->userRegistrantThree_p1->registrant->registeredTime = (new \DateTime('-24 hour'));
        
        $this->viewAllUri .= "?pageSize=1";
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '3']);
        $this->seeJsonDoesntContains(['id' => $this->clientRegistrantOne_p1->id]);
        $this->seeJsonContains(['id' => $this->teamRegistrantTwo_p2->id]);
        $this->seeJsonDoesntContains(['id' => $this->userRegistrantThree_p1->id]);
    }
}

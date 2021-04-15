<?php

namespace Tests\Controllers\Personnel;

use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientRegistrant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfRegistrant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramRegistration;
use Tests\Controllers\RecordPreparation\RecordOfUser;
use Tests\Controllers\RecordPreparation\User\RecordOfUserRegistrant;

class RegistrantControllerTest extends PersonnelTestCase
{
    protected $registrantUri;
    protected $registrantOne_client;
    protected $registrantTwo_team;
    protected $registrantThree_user;
    protected $clientRegistrant;
    protected $teamRegistrant;
    protected $userRegistrant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registrantUri = $this->personnelUri . "/registrants";
        
        $this->connection->table('Program')->truncate();
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('ClientRegistrant')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('TeamRegistrant')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('UserRegistrant')->truncate();
        $this->connection->table('Coordinator')->truncate();
        
        $firm = $this->personnel->firm;
        
        $programOne = new RecordOfProgram($firm, '1');
        $programTwo = new RecordOfProgram($firm, '2');
        $programThree = new RecordOfProgram($firm, '3');
        $this->connection->table('Program')->insert($programOne->toArrayForDbEntry());
        $this->connection->table('Program')->insert($programTwo->toArrayForDbEntry());
        $this->connection->table('Program')->insert($programThree->toArrayForDbEntry());
        
        $this->registrantOne_client = new RecordOfRegistrant($programOne, '1');
        $this->registrantOne_client->concluded = true;
        $this->registrantTwo_team = new RecordOfRegistrant($programTwo, '2');
        $this->registrantThree_user = new RecordOfRegistrant($programThree, '3');
        $this->connection->table('Registrant')->insert($this->registrantOne_client->toArrayForDbEntry());
        $this->connection->table('Registrant')->insert($this->registrantTwo_team->toArrayForDbEntry());
        $this->connection->table('Registrant')->insert($this->registrantThree_user->toArrayForDbEntry());
        
        $clientOne = new RecordOfClient($firm, '1');
        $this->connection->table('Client')->insert($clientOne->toArrayForDbEntry());
        
        $this->clientRegistrant = new RecordOfClientRegistrant($clientOne, $this->registrantOne_client);
        $this->connection->table('ClientRegistrant')->insert($this->clientRegistrant->toArrayForDbEntry());
        
        $teamOne = new RecordOfTeam($firm, $clientOne, '1');
        $this->connection->table('Team')->insert($teamOne->toArrayForDbEntry());
        
        $this->teamRegistrant = new RecordOfTeamProgramRegistration($teamOne, $this->registrantTwo_team);
        $this->connection->table('TeamRegistrant')->insert($this->teamRegistrant->toArrayForDbEntry());
        
        $userOne = new RecordOfUser('1');
        $this->connection->table('User')->insert($userOne->toArrayForDbEntry());
        
        $this->userRegistrant = new RecordOfUserRegistrant($userOne, $this->registrantThree_user);
        $this->connection->table('UserRegistrant')->insert($this->userRegistrant->toArrayForDbEntry());
        
        $coordinatorOne = new RecordOfCoordinator($programOne, $this->personnel, '1');
        $coordinatorTwo = new RecordOfCoordinator($programTwo, $this->personnel, '2');
        $coordinatorThree = new RecordOfCoordinator($programThree, $this->personnel, '3');
        $coordinatorThree->active = false;
        $this->connection->table('Coordinator')->insert($coordinatorOne->toArrayForDbEntry());
        $this->connection->table('Coordinator')->insert($coordinatorTwo->toArrayForDbEntry());
        $this->connection->table('Coordinator')->insert($coordinatorThree->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('ClientRegistrant')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('TeamRegistrant')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('UserRegistrant')->truncate();
        $this->connection->table('Coordinator')->truncate();
    }
    
    public function test_showAll_200()
    {
        $response = [
            'total' => 2,
            'list' => [
                [
                    "id" => $this->registrantOne_client->id,
                    "registeredTime" => $this->registrantOne_client->registeredTime,
                    "note" => $this->registrantOne_client->note,
                    "concluded" => $this->registrantOne_client->concluded,
                    "user" => null,
                    "client" => [
                        'id' => $this->clientRegistrant->client->id,
                        'name' => $this->clientRegistrant->client->getFullName(),
                    ],
                    "team" => null,
                    "program" => [
                        "id" => $this->registrantOne_client->program->id,
                        "name" => $this->registrantOne_client->program->name,
                    ],
                ],
                [
                    "id" => $this->registrantTwo_team->id,
                    "registeredTime" => $this->registrantTwo_team->registeredTime,
                    "note" => $this->registrantTwo_team->note,
                    "concluded" => $this->registrantTwo_team->concluded,
                    "user" => null,
                    "client" => null,
                    "team" => [
                        'id' => $this->teamRegistrant->team->id,
                        'name' => $this->teamRegistrant->team->name,
                    ],
                    "program" => [
                        "id" => $this->registrantTwo_team->program->id,
                        "name" => $this->registrantTwo_team->program->name,
                    ],
                ],
            ],
        ];
        $this->get($this->registrantUri, $this->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_concludedStatusFilterSet_200()
    {
        $uri = $this->registrantUri . "?concludedStatus=false";
        $this->get($uri, $this->personnel->token)
                ->seeStatusCode(200);
        
        $response = [
            'total' => 1,
            'list' => [
                [
                    "id" => $this->registrantTwo_team->id,
                    "registeredTime" => $this->registrantTwo_team->registeredTime,
                    "note" => $this->registrantTwo_team->note,
                    "concluded" => $this->registrantTwo_team->concluded,
                    "user" => null,
                    "client" => null,
                    "team" => [
                        'id' => $this->teamRegistrant->team->id,
                        'name' => $this->teamRegistrant->team->name,
                    ],
                    "program" => [
                        "id" => $this->registrantTwo_team->program->id,
                        "name" => $this->registrantTwo_team->program->name,
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
}

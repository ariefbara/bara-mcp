<?php

namespace Tests\Controllers\Manager;

use DateTime;
use Tests\Controllers\ {
    Manager\ManagerTestCase,
    RecordPreparation\Firm\Program\RecordOfConsultant,
    RecordPreparation\Firm\Program\RecordOfCoordinator,
    RecordPreparation\Firm\RecordOfPersonnel,
    RecordPreparation\Firm\RecordOfProgram
};

class PersonnelControllerTest extends ManagerTestCase
{
    protected $uri;
    protected $personnel, $personnelOne_disable;
    protected $program;
    protected $inactiveCoordinator;
    protected $inactiveMentor;
    protected $personnelInput = [
        "firstName" => 'firstname',
        "lastName" => 'lastname',
        "email" => 'new_personnel@email.org',
        "password" => 'password213',
        "phone" => '081231231231',
        "bio" => 'new personnel bio',
    ];


    protected function setUp(): void
    {
        parent::setUp();
        $this->uri = $this->managerUri . "/personnels";
        
        $this->connection->table('Personnel')->truncate();
        $this->connection->table("Program")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Consultant")->truncate();
        
        $this->personnel = new RecordOfPersonnel($this->firm, 0, 'personnel@email.org', 'password123');
        $this->personnelOne_disable = new RecordOfPersonnel($this->firm, 1, 'personnel_one@email.org', 'password123');
        $this->personnelOne_disable->active = false;
        $this->connection->table('Personnel')->insert($this->personnel->toArrayForDbEntry());
        $this->connection->table('Personnel')->insert($this->personnelOne_disable->toArrayForDbEntry());
        
        $this->program = new RecordOfProgram($this->manager->firm, 0);
        $this->connection->table('Program')->insert($this->program->toArrayForDbEntry());
        
        $this->inactiveCoordinator = new RecordOfCoordinator($this->program, $this->personnel, 0);
        $this->inactiveCoordinator->active = false;
        $this->connection->table('Coordinator')->insert($this->inactiveCoordinator->toArrayForDbEntry());
        
        $this->inactiveConsultant = new RecordOfConsultant($this->program, $this->personnel, 0);
        $this->inactiveConsultant->active = false;
        $this->connection->table('Consultant')->insert($this->inactiveConsultant->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table("Program")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Consultant")->truncate();
    }
    
    public function test_add()
    {
        $response = [
            "name" => $this->personnelInput['firstName'] . " " . $this->personnelInput['lastName'],
            "email" => $this->personnelInput['email'],
            "phone" => $this->personnelInput['phone'],
            "bio" => $this->personnelInput['bio'],
            "joinTime" => (new DateTime())->format('Y-m-d H:i:s'),
        ];
        $this->post($this->uri, $this->personnelInput, $this->manager->token)
            ->seeStatusCode(201)
            ->seeJsonContains($response);
        
        $personnelRecord = [
            "Firm_id" => $this->firm->id,
            "firstName" => $this->personnelInput['firstName'],
            "lastName" => $this->personnelInput['lastName'],
            "email" => $this->personnelInput['email'],
            "phone" => $this->personnelInput['phone'],
            "bio" => $this->personnelInput['bio'],
            "joinTime" => (new DateTime())->format('Y-m-d H:i:s'),
            "active" => true,
        ];
        $this->seeInDatabase('Personnel', $personnelRecord);
    }
    public function test_add_emptyName_error400()
    {
        $this->personnelInput['firstName'] = '';
        $this->post($this->uri, $this->personnelInput, $this->manager->token)
            ->seeStatusCode(400);
    }
    public function test_add_invalidEmailFormat_error400()
    {
        $this->personnelInput['email'] = 'bad-format';
        $this->post($this->uri, $this->personnelInput, $this->manager->token)
            ->seeStatusCode(400);
    }
    public function test_add_duplicateEmail_error409()
    {
        $this->personnelInput['email'] = $this->personnel->email;
        $this->post($this->uri, $this->personnelInput, $this->manager->token)
            ->seeStatusCode(409);
    }
    public function test_add_userNotManager_error401()
    {
        $this->post($this->uri, $this->personnelInput, $this->removedManager->token)
            ->seeStatusCode(401);
    }
    
    public function test_disable_200()
    {
        $uri = $this->uri . "/{$this->personnel->id}";
        $this->delete($uri, [], $this->manager->token)
                ->seeStatusCode(200);
        
        $personnelEntry = [
            "id" => $this->personnel->id,
            "active" => false,
        ];
        $this->seeInDatabase("Personnel", $personnelEntry);
    }
    public function test_disable_hasActiveProgramCoordinatorships_403()
    {
        $coordinator = new RecordOfCoordinator($this->program, $this->personnel, 1);
        $this->connection->table("Coordinator")->insert($coordinator->toArrayForDbEntry());
        $uri = $this->uri . "/{$this->personnel->id}";
        $this->delete($uri, [], $this->manager->token)
                ->seeStatusCode(403);
    }
    public function test_disable_hasActiveProgramMentorships_403()
    {
        $consultant = new RecordOfConsultant($this->program, $this->personnel, 99);
        $this->connection->table("Consultant")->insert($consultant->toArrayForDbEntry());
        
        $uri = $this->uri . "/{$this->personnel->id}";
        $this->delete($uri, [], $this->manager->token)
                ->seeStatusCode(403);
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->personnel->id,
            "name" => $this->personnel->getFullName(),
            "email" => $this->personnel->email,
            "phone" => $this->personnel->phone,
            "bio" => $this->personnel->bio,
            "joinTime" => $this->personnel->joinTime,
        ];
        $uri = $this->uri . "/{$this->personnel->id}";
        $this->get($uri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_show_userNotManager_error401()
    {
        $uri = $this->uri . "/{$this->personnel->id}";
        $this->get($uri, $this->removedManager->token)
            ->seeStatusCode(401);
    }
    
    public function test_showAll()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->personnel->id,
                    "name" => $this->personnel->getFullName(),
                ],
                [
                    "id" => $this->personnelOne_disable->id,
                    "name" => $this->personnelOne_disable->getFullName(),
                ],
            ],
        ];
        $this->get($this->uri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_showAll_activeStatusFilterSet()
    {
        $totalResponse = ["total" => 1];
        $personnelResponse = [
            "id" => $this->personnel->id,
        ];
        
        $uri = $this->uri . "?activeStatus=true";
        $this->get($uri, $this->manager->token)
                ->seeStatusCode(200)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($personnelResponse);
    }
    public function test_showAll_userNotManager_error401()
    {
        $this->get($this->uri, $this->removedManager->token)
            ->seeStatusCode(401);
        
    }
}

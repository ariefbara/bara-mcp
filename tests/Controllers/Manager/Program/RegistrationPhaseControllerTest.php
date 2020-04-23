<?php

namespace Tests\Controllers\Manager\Program;

use DateTime;
use Tests\Controllers\ {
    Manager\ProgramTestCase,
    RecordPreparation\Firm\Program\RecordOfRegistrationPhase
};

class RegistrationPhaseControllerTest extends ProgramTestCase
{
    protected $registrationPhaseUri;
    protected $registrationPhase, $registrationPhaseOne;
    protected $registrationPhaseInput;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->registrationPhaseUri = $this->programUri . "/{$this->program->id}/registration-phases";
        $this->connection->table('RegistrationPhase')->truncate();
        
        $this->registrationPhase = new RecordOfRegistrationPhase($this->program, 0);
        $this->registrationPhaseOne = new RecordOfRegistrationPhase($this->program, 1);
        $this->connection->table('RegistrationPhase')->insert($this->registrationPhase->toArrayForDbEntry());
        $this->connection->table('RegistrationPhase')->insert($this->registrationPhaseOne->toArrayForDbEntry());
        
        $this->registrationPhaseInput = [
            "name" => 'new registration phase name',
            "startDate" => (new DateTime("+7 days"))->format('Y-m-d'),
            "endDate" => (new DateTime("+30 days"))->format('Y-m-d'),
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('RegistrationPhase')->truncate();
    }
    
    public function test_add()
    {
        $reponse = [
            "name" => $this->registrationPhaseInput['name'],
            "startDate" => $this->registrationPhaseInput['startDate'],
            "endDate" => $this->registrationPhaseInput['endDate'],
        ];
        
        $this->post($this->registrationPhaseUri, $this->registrationPhaseInput, $this->manager->token)
            ->seeStatusCode(201)
            ->seeJsonContains($reponse);
        
        $registrationPhaseEntry = [
            "Program_id" => $this->program->id,
            "name" => $this->registrationPhaseInput['name'],
            "startDate" => $this->registrationPhaseInput['startDate'],
            "endDate" => $this->registrationPhaseInput['endDate'],
            "removed" => false,
        ];
        $this->seeInDatabase('RegistrationPhase', $registrationPhaseEntry);
    }
    public function test_add_userNotProgramManager_error401()
    {
        $this->post($this->registrationPhaseUri, $this->registrationPhaseInput, $this->removedManager->token)
            ->seeStatusCode(401);
    }
    
    public function test_update()
    {
        $reponse = [
            "id" => $this->registrationPhase->id,
            "name" => $this->registrationPhaseInput['name'],
            "startDate" => $this->registrationPhaseInput['startDate'],
            "endDate" => $this->registrationPhaseInput['endDate'],
        ];
        
        $uri = $this->registrationPhaseUri . "/{$this->registrationPhase->id}";
        $this->patch($uri, $this->registrationPhaseInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($reponse);
        
        $registrationPhaseEntry = [
            "id" => $this->registrationPhase->id,
            "Program_id" => $this->program->id,
            "name" => $this->registrationPhaseInput['name'],
            "startDate" => $this->registrationPhaseInput['startDate'],
            "endDate" => $this->registrationPhaseInput['endDate'],
            "removed" => false,
        ];
        $this->seeInDatabase('RegistrationPhase', $registrationPhaseEntry);
    }
    public function test_update_userNotProgramManager_error401()
    {
        $uri = $this->registrationPhaseUri . "/{$this->registrationPhase->id}";
        $this->patch($uri, $this->registrationPhaseInput, $this->removedManager->token)
            ->seeStatusCode(401);
    }
    
    public function test_remove()
    {
        $uri = $this->registrationPhaseUri . "/{$this->registrationPhase->id}";
        $this->delete($uri, [], $this->manager->token)
            ->seeStatusCode(200);
        
        $registrationPhaseEntry = [
            "id" => $this->registrationPhase->id,
            "Program_id" => $this->program->id,
            "removed" => true,
        ];
        $this->seeInDatabase('RegistrationPhase', $registrationPhaseEntry);
    }
    public function test_remove_userNotProgramManager_error401()
    {
        $uri = $this->registrationPhaseUri . "/{$this->registrationPhase->id}";
        $this->delete($uri, [], $this->removedManager->token)
            ->seeStatusCode(401);
    }
    
    public function test_show()
    {
        $reponse = [
            "id" => $this->registrationPhase->id,
            "name" => $this->registrationPhase->name,
            "startDate" => $this->registrationPhase->startDate,
            "endDate" => $this->registrationPhase->endDate,
        ];
        $uri = $this->registrationPhaseUri . "/{$this->registrationPhase->id}";
        $this->get($uri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($reponse);
    }
    public function test_show_userNotProgramManager_error401()
    {
        $uri = $this->registrationPhaseUri . "/{$this->registrationPhase->id}";
        $this->get($uri, $this->removedManager->token)
            ->seeStatusCode(401);
    }
    
    public function test_showAll()
    {
        $response = [
            "total" => 2, 
            "list" => [
                [
                    "id" => $this->registrationPhase->id,
                    "name" => $this->registrationPhase->name,
                ],
                [
                    "id" => $this->registrationPhaseOne->id,
                    "name" => $this->registrationPhaseOne->name,
                ],
            ],
        ];
        $this->get($this->registrationPhaseUri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_showAll_userNotProgramManager_error401()
    {
        $this->get($this->registrationPhaseUri, $this->removedManager->token)
            ->seeStatusCode(401);
    }
    
}

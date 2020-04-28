<?php

namespace Tests\Controllers\Manager\Program;

use Tests\Controllers\ {
    Manager\ProgramTestCase,
    RecordPreparation\Firm\Program\RecordOfConsultant,
    RecordPreparation\Firm\RecordOfPersonnel
};

class ConsultantControllerTest extends ProgramTestCase
{
    protected $consultantUri;
    protected $consultant, $consultant1;
    protected $personnel, $personnel1, $personnel2;
    protected $consultantInput;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultantUri = $this->programUri . "/{$this->program->id}/consultants";
        
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        
        $this->personnel = new RecordOfPersonnel($this->firm, 0, 'personnel@email.org', 'password123');
        $this->personnel1 = new RecordOfPersonnel($this->firm, 1, 'personnel1@email.org', 'password123');
        $this->personnel2 = new RecordOfPersonnel($this->firm, 2, 'personnel2@email.org', 'password123');
        $this->connection->table('Personnel')->insert($this->personnel->toArrayForDbEntry());
        $this->connection->table('Personnel')->insert($this->personnel1->toArrayForDbEntry());
        $this->connection->table('Personnel')->insert($this->personnel2->toArrayForDbEntry());
        
        $this->consultant = new RecordOfConsultant($this->program, $this->personnel, 0);
        $this->consultant1 = new RecordOfConsultant($this->program, $this->personnel1, 1);
        $this->connection->table('Consultant')->insert($this->consultant->toArrayForDbEntry());
        $this->connection->table('Consultant')->insert($this->consultant1->toArrayForDbEntry());
        
        $this->consultantInput = [
            "personnelId" => $this->personnel2->id,
        ];
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
    }
    
    public function test_assign()
    {
        $response = [
            "personnel" => [
                "id" => $this->personnel2->id,
                "name" => $this->personnel2->name,
            ],
        ];
        
        $this->put($this->consultantUri, $this->consultantInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $consultantRecord = [
            "Program_id" => $this->program->id,
            "Personnel_id" => $this->personnel2->id,
            "removed" => false,
        ];
        $this->seeInDatabase('Consultant', $consultantRecord);
    }
    public function test_assign_userNotManager_error401()
    {
        $this->put($this->consultantUri, $this->consultantInput, $this->removedManager->token)
            ->seeStatusCode(401);
    }
    
    public function test_remove()
    {
        $uri = $this->consultantUri . "/{$this->consultant->id}";
        $this->delete($uri, [], $this->manager->token)
            ->seeStatusCode(200);
        
        $consultantRecord = [
            "id" => $this->consultant->id,
            "removed" => true,
        ];
        $this->seeInDatabase('Consultant', $consultantRecord);
    }
    public function test_remove_userNotManager_error401()
    {
        $uri = $this->consultantUri . "/{$this->consultant->id}";
        $this->delete($uri, [], $this->removedManager->token)
            ->seeStatusCode(401);
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->consultant->id,
            "personnel" => [
                "id" => $this->consultant->personnel->id,
                "name" => $this->consultant->personnel->name,
            ],
        ];
        $uri = $this->consultantUri . "/{$this->consultant->id}";
        $this->get($uri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_show_userNotManager_error401()
    {
        $uri = $this->consultantUri . "/{$this->consultant->id}";
        $this->get($uri, $this->removedManager->token)
            ->seeStatusCode(401);
    }
    
    public function test_showAll()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->consultant->id,
                    "personnel" => [
                        "id" => $this->consultant->personnel->id,
                        "name" => $this->consultant->personnel->name,
                    ],
                ],
                [
                    "id" => $this->consultant1->id,
                    "personnel" => [
                        "id" => $this->consultant1->personnel->id,
                        "name" => $this->consultant1->personnel->name,
                    ],
                ],
            ],
        ];
        $this->get($this->consultantUri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_showAll_userNotManager_error401()
    {
        $this->get($this->consultantUri, $this->removedManager->token)
            ->seeStatusCode(401);
    }
}

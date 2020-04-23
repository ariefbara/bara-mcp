<?php

namespace Tests\Controllers\Manager\Program;

use Tests\Controllers\ {
    Manager\ProgramTestCase,
    RecordPreparation\Firm\Program\RecordOfMentor,
    RecordPreparation\Firm\RecordOfPersonnel
};

class MentorControllerTest extends ProgramTestCase
{
    protected $mentorUri;
    protected $mentor, $mentor1;
    protected $personnel, $personnel1, $personnel2;
    protected $mentorInput;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->mentorUri = $this->programUri . "/{$this->program->id}/mentors";
        
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Mentor')->truncate();
        
        $this->personnel = new RecordOfPersonnel($this->firm, 0, 'personnel@email.org', 'password123');
        $this->personnel1 = new RecordOfPersonnel($this->firm, 1, 'personnel1@email.org', 'password123');
        $this->personnel2 = new RecordOfPersonnel($this->firm, 2, 'personnel2@email.org', 'password123');
        $this->connection->table('Personnel')->insert($this->personnel->toArrayForDbEntry());
        $this->connection->table('Personnel')->insert($this->personnel1->toArrayForDbEntry());
        $this->connection->table('Personnel')->insert($this->personnel2->toArrayForDbEntry());
        
        $this->mentor = new RecordOfMentor($this->program, $this->personnel, 0);
        $this->mentor1 = new RecordOfMentor($this->program, $this->personnel1, 1);
        $this->connection->table('Mentor')->insert($this->mentor->toArrayForDbEntry());
        $this->connection->table('Mentor')->insert($this->mentor1->toArrayForDbEntry());
        
        $this->mentorInput = [
            "personnelId" => $this->personnel2->id,
        ];
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Mentor')->truncate();
    }
    
    public function test_assign()
    {
        $response = [
            "personnel" => [
                "id" => $this->personnel2->id,
                "name" => $this->personnel2->name,
            ],
        ];
        
        $this->put($this->mentorUri, $this->mentorInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $mentorRecord = [
            "Program_id" => $this->program->id,
            "Personnel_id" => $this->personnel2->id,
            "removed" => false,
        ];
        $this->seeInDatabase('Mentor', $mentorRecord);
    }
    public function test_assign_userNotManager_error401()
    {
        $this->put($this->mentorUri, $this->mentorInput, $this->removedManager->token)
            ->seeStatusCode(401);
    }
    
    public function test_remove()
    {
        $uri = $this->mentorUri . "/{$this->mentor->id}";
        $this->delete($uri, [], $this->manager->token)
            ->seeStatusCode(200);
        
        $mentorRecord = [
            "id" => $this->mentor->id,
            "removed" => true,
        ];
        $this->seeInDatabase('Mentor', $mentorRecord);
    }
    public function test_remove_userNotManager_error401()
    {
        $uri = $this->mentorUri . "/{$this->mentor->id}";
        $this->delete($uri, [], $this->removedManager->token)
            ->seeStatusCode(401);
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->mentor->id,
            "personnel" => [
                "id" => $this->mentor->personnel->id,
                "name" => $this->mentor->personnel->name,
            ],
        ];
        $uri = $this->mentorUri . "/{$this->mentor->id}";
        $this->get($uri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_show_userNotManager_error401()
    {
        $uri = $this->mentorUri . "/{$this->mentor->id}";
        $this->get($uri, $this->removedManager->token)
            ->seeStatusCode(401);
    }
    
    public function test_showAll()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->mentor->id,
                    "personnel" => [
                        "id" => $this->mentor->personnel->id,
                        "name" => $this->mentor->personnel->name,
                    ],
                ],
                [
                    "id" => $this->mentor1->id,
                    "personnel" => [
                        "id" => $this->mentor1->personnel->id,
                        "name" => $this->mentor1->personnel->name,
                    ],
                ],
            ],
        ];
        $this->get($this->mentorUri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_showAll_userNotManager_error401()
    {
        $this->get($this->mentorUri, $this->removedManager->token)
            ->seeStatusCode(401);
    }
}

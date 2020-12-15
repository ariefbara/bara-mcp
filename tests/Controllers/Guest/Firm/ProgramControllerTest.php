<?php

namespace App\Http\Controllers\Guest\Firm;

use Tests\Controllers\ {
    Guest\FirmTestCase,
    RecordPreparation\Firm\RecordOfProgram
};

class ProgramControllerTest extends FirmTestCase
{
    protected $programUri;
    protected $program;
    protected $programOne;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programUri = $this->firmUri . "/{$this->firm->id}/programs";
        $this->connection->table('Program')->truncate();
        
        $this->program = new RecordOfProgram($this->firm, 0);
        $this->program->published = true;
        $this->programOne = new RecordOfProgram($this->firm, 1);
        $this->programOne->published = true;
        $this->connection->table('Program')->insert($this->program->toArrayForDbEntry());
        $this->connection->table('Program')->insert($this->programOne->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->program->id,
            "name" => $this->program->name,
            "description" => $this->program->description,
            "published" => $this->program->published,
        ];
        $uri = $this->programUri . "/{$this->program->id}";
        $this->get($uri)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_showAll()
    {
        $response = [
            "total" => 2, 
            "list" => [
                [
                    "id" => $this->program->id,
                    "name" => $this->program->name,
                    "description" => $this->program->description,
                    "published" => $this->program->published,
                ],
                [
                    "id" => $this->programOne->id,
                    "name" => $this->programOne->name,
                    "description" => $this->programOne->description,
                    "published" => $this->programOne->published,
                ],
            ],
        ];
        $this->get($this->programUri)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
}

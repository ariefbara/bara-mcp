<?php

namespace Tests\Controllers\Guest;

use Tests\Controllers\ControllerTestCase;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\RecordOfFirm;

class ProgramControllerTest extends ControllerTestCase
{
    protected $programUri = '/api/guest/programs';
    
    protected $programOne;
    protected $programTwo_unpublished;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Program')->truncate();
        
        $firm = new RecordOfFirm('99');
        $this->connection->table('Firm')->insert($firm->toArrayForDbEntry());
        
        $this->programOne = new RecordOfProgram($firm, '1');
        $this->programTwo_unpublished = new RecordOfProgram($firm, '2');
        $this->programTwo_unpublished->published = false;
        $this->connection->table('Program')->insert($this->programOne->toArrayForDbEntry());
        $this->connection->table('Program')->insert($this->programTwo_unpublished->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_show_200()
    {
        $uri = $this->programUri . "/{$this->programOne->id}";
        $this->get($uri);
        $this->seeStatusCode(200);
        $response = [
            'id' => $this->programOne->id,
            'name' => $this->programOne->name,
            'description' => $this->programOne->description,
            'firm' => [
                'id' => $this->programOne->firm->id,
                'name' => $this->programOne->firm->name,
            ],
        ];
        $this->seeJsonContains($response);
    }
    
    public function test_showAll_200()
    {
$this->disableExceptionHandling();
        $this->get($this->programUri);
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        $programOneResponse = [
            'id' => $this->programOne->id,
            'name' => $this->programOne->name,
            'description' => $this->programOne->description,
            'firm' => [
                'id' => $this->programOne->firm->id,
                'name' => $this->programOne->firm->name,
            ],
        ];
        $this->seeJsonContains($programOneResponse);
    }
}

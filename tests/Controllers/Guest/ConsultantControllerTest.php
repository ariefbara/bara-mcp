<?php

namespace Tests\Controllers\Guest;

use Tests\Controllers\ControllerTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\RecordOfFirm;

class ConsultantControllerTest extends ControllerTestCase
{
    protected $program;
    protected $consultantOne;
    protected $consultantTwo_inactive;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        
        $firm = new RecordOfFirm('99');
        $this->connection->table('Firm')->insert($firm->toArrayForDbEntry());
        
        $this->program = new RecordOfProgram($firm, '99');
        $this->connection->table('Program')->insert($this->program->toArrayForDbEntry());
        
        $personnelOne = new RecordOfPersonnel($firm, '1');
        $personnelTwo = new RecordOfPersonnel($firm, '2');
        $this->connection->table('Personnel')->insert($personnelOne->toArrayForDbEntry());
        $this->connection->table('Personnel')->insert($personnelTwo->toArrayForDbEntry());
        
        $this->consultantOne = new RecordOfConsultant($this->program, $personnelOne, '1');
        $this->consultantTwo_inactive = new RecordOfConsultant($this->program, $personnelOne, '2');
        $this->consultantTwo_inactive->active = false;
        $this->connection->table('Consultant')->insert($this->consultantOne->toArrayForDbEntry());
        $this->connection->table('Consultant')->insert($this->consultantTwo_inactive->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
    }
    
    public function test_show_200()
    {
        $uri = "/api/guest/consultants/{$this->consultantOne->id}";
        $this->get($uri);
        $this->seeStatusCode(200);
echo $uri;
$this->seeJsonContains(['print']);
        
        $response = [
            'id' => $this->consultantOne->id,
            'personnel' => [
                'id' => $this->consultantOne->personnel->id,
                'name' => $this->consultantOne->personnel->getFullName(),
                'bio' => $this->consultantOne->personnel->bio,
            ],
        ];
        $this->seeJsonContains($response);
    }
    
    public function test_showAll_200()
    {
        $uri = "/api/guest/programs/{$this->program->id}/consultants";
        $this->get($uri);
        $this->seeStatusCode(200);
//echo $uri;
//$this->seeJsonContains(['print']);

        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        
        $response = [
            'id' => $this->consultantOne->id,
            'personnel' => [
                'id' => $this->consultantOne->personnel->id,
                'name' => $this->consultantOne->personnel->getFullName(),
                'bio' => $this->consultantOne->personnel->bio,
            ],
        ];
        $this->seeJsonContains($response);
    }
}

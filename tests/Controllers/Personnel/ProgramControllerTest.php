<?php

namespace Tests\Controllers\Personnel;

use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;

class ProgramControllerTest extends PersonnelTestCase
{
    protected $listOfCoordinatedProgramUri;
    protected $coordinatorOne;
    protected $coordinatorTwo;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Coordinator')->truncate();
        
        $this->listOfCoordinatedProgramUri = $this->personnelUri . "/list-of-coordinated-program";
        
        $firm = $this->personnel->firm;
        
        $programOne = new RecordOfProgram($firm, 1);
        $programTwo = new RecordOfProgram($firm, 2);
        
        $this->coordinatorOne = new RecordOfCoordinator($programOne, $this->personnel, 1);
        $this->coordinatorTwo = new RecordOfCoordinator($programTwo, $this->personnel, 2);
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Coordinator')->truncate();
    }
    
    protected function listOfCoordinatedProgram()
    {
        $this->coordinatorOne->program->insert($this->connection);
        $this->coordinatorTwo->program->insert($this->connection);
        
        $this->coordinatorOne->insert($this->connection);
        $this->coordinatorTwo->insert($this->connection);
        
echo $this->listOfCoordinatedProgramUri;
        $this->get($this->listOfCoordinatedProgramUri, $this->personnel->token);
    }
    public function test_listOfCoordinatedProgram_200()
    {
        $this->listOfCoordinatedProgram();
        $this->seeStatusCode(200);
$this->seeJsonContains(['print']);
        
        $this->seeJsonContains([
            'id' => $this->coordinatorOne->program->id,
            'name' => $this->coordinatorOne->program->name,
            'coordinatorId' => $this->coordinatorOne->id,
        ]);
        $this->seeJsonContains([
            'id' => $this->coordinatorTwo->program->id,
            'name' => $this->coordinatorTwo->program->name,
            'coordinatorId' => $this->coordinatorTwo->id,
        ]);
    }
    public function test_listOfCoordinatedProgram_excludeNonCoordinatedProgram()
    {
        $programOther = new RecordOfProgram($this->personnel->firm, 'other');
        $programOther->insert($this->connection);
        
        $this->listOfCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['id' => $this->coordinatorOne->program->id]);
        $this->seeJsonContains(['id' => $this->coordinatorTwo->program->id]);
        $this->seeJsonDoesntContains(['id' => $programOther->id]);
    }
    public function test_listOfCoordinatedProgram_excludeProgramInInactiveCoordinator()
    {
        $this->coordinatorTwo->active = false;
        
        $this->listOfCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['id' => $this->coordinatorOne->program->id]);
        $this->seeJsonDoesntContains(['id' => $this->coordinatorTwo->program->id]);
    }
}

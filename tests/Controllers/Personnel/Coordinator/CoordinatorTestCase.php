<?php

namespace Tests\Controllers\Personnel\Coordinator;

use Tests\Controllers\ {
    Personnel\PersonnelTestCase,
    RecordPreparation\Firm\Program\RecordOfCoordinator,
    RecordPreparation\Firm\RecordOfProgram
};

class CoordinatorTestCase extends PersonnelTestCase
{
    protected $coordinatorUri;
    /**
     *
     * @var RecordOfCoordinator
     */
    protected $coordinator;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinatorUri = $this->personnelUri . "/coordinators";
        
        $this->connection->table("Program")->truncate();
        $this->connection->table("Coordinator")->truncate();
        
        $firm = $this->personnel->firm;
        
        $program = new RecordOfProgram($firm, 999);
        $this->connection->table("Program")->insert($program->toArrayForDbEntry());
        
        $this->coordinator = new RecordOfCoordinator($program, $this->personnel, 999);
        $this->connection->table("Coordinator")->insert($this->coordinator->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Program")->truncate();
        $this->connection->table("Coordinator")->truncate();
    }
}

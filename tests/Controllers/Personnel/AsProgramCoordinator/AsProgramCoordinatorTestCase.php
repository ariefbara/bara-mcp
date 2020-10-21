<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

use Tests\Controllers\ {
    Personnel\PersonnelTestCase,
    RecordPreparation\Firm\Program\RecordOfCoordinator,
    RecordPreparation\Firm\RecordOfPersonnel,
    RecordPreparation\Firm\RecordOfProgram
};

class AsProgramCoordinatorTestCase extends PersonnelTestCase
{
    protected $asProgramCoordinatorUri;
    /**
     *
     * @var RecordOfCoordinator
     */
    protected $coordinator;
    /**
     *
     * @var RecordOfCoordinator
     */
    protected $removedCoordinator;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Coordinator')->truncate();
        $this->connection->table('Program')->truncate();
        
        $program = new RecordOfProgram($this->personnel->firm, 999);
        $this->connection->table("Program")->insert($program->toArrayForDbEntry());
        
        $personnel = new RecordOfPersonnel($program->firm, 'removed_coordinator', 'removed_coordinantor', 'password123');
        $this->connection->table("Personnel")->insert($personnel->toArrayForDbEntry());
        
        $this->coordinator = new RecordOfCoordinator($program, $this->personnel, 999);
        $this->removedCoordinator = new RecordOfCoordinator($program, $personnel, 998);
        $this->removedCoordinator->removed = true;
        $this->connection->table("Coordinator")->insert($this->coordinator->toArrayForDbEntry());
        $this->connection->table("Coordinator")->insert($this->removedCoordinator->toArrayForDbEntry());
        
        
        $this->asProgramCoordinatorUri = $this->personnelUri . "/as-program-coordinator/{$program->id}";
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Coordinator')->truncate();
        $this->connection->table('Program')->truncate();
    }
}

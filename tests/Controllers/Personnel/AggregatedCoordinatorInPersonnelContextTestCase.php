<?php

namespace Tests\Controllers\Personnel;

use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;

class AggregatedCoordinatorInPersonnelContextTestCase extends ExtendedPersonnelTestCase
{

    /**
     * 
     * @var RecordOfCoordinator
     */
    protected $coordinatorOne;

    /**
     * 
     * @var RecordOfCoordinator
     */
    protected $coordinatorTwo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Coordinator')->truncate();
        
        $programOne = new RecordOfProgram($this->personnel->firm, 1);
        $programTwo = new RecordOfProgram($this->personnel->firm, 2);

        $this->coordinatorOne = new RecordOfCoordinator($programOne, $this->personnel, 1);
        $this->coordinatorTwo = new RecordOfCoordinator($programTwo, $this->personnel, 2);
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Coordinator')->truncate();
    }
    
    protected function persistAggregatedCoordinatorDependency()
    {
        $this->persistPersonnelDependency();
        
        $this->coordinatorOne->program->insert($this->connection);
        $this->coordinatorTwo->program->insert($this->connection);
        
        $this->coordinatorOne->insert($this->connection);
        $this->coordinatorTwo->insert($this->connection);
    }

}

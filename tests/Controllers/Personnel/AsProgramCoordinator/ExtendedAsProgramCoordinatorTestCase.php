<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

use Tests\Controllers\ControllerTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\RecordOfFirm;

class ExtendedAsProgramCoordinatorTestCase extends ControllerTestCase
{
    /**
     * 
     * @var RecordOfCoordinator
     */
    protected $coordinatorOne;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Coordinator')->truncate();
        
        $firmOne = new RecordOfFirm('1');
        $programOne = new RecordOfProgram($firmOne, '1');
        $personnelOne = new RecordOfPersonnel($firmOne, '1');
        $this->coordinatorOne = new RecordOfCoordinator($programOne, $personnelOne, '1');
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Coordinator')->truncate();
    }
    
    protected function persistCoordinatorDependency()
    {
        $this->coordinatorOne->program->firm->insert($this->connection);
        $this->coordinatorOne->program->insert($this->connection);
        $this->coordinatorOne->personnel->insert($this->connection);
        $this->coordinatorOne->insert($this->connection);
    }

}

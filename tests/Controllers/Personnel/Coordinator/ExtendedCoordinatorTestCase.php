<?php

namespace Tests\Controllers\Personnel\Coordinator;

use Tests\Controllers\Personnel\PersonnelTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;

class ExtendedCoordinatorTestCase extends PersonnelTestCase
{

    /**
     * 
     * @var RecordOfCoordinator
     */
    protected $coordinator;
    protected $coordinatorUri;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Coordinator')->truncate();

        $program = new RecordOfProgram($this->personnel->firm, '99');
        $this->coordinator = new RecordOfCoordinator($program, $this->personnel, '99');

        $this->coordinatorUri = $this->personnelUri . "/coordinators/{$this->coordinator->id}";
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Coordinator')->truncate();
    }

    protected function persistCoordinatorDependency()
    {
        $this->coordinator->program->insert($this->connection);
        $this->coordinator->insert($this->connection);
    }

}

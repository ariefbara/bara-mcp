<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;

class ProgramTestCase extends ManagerTestCase
{

    protected $programUri;

    /**
     *
     * @var RecordOfProgram
     */
    protected $program;

    protected function setUp(): void
    {
        parent::setUp();
        $this->programUri = $this->managerUri . "/programs";
        $this->connection->table('Program')->truncate();

        $this->program = new RecordOfProgram($this->firm, 0);
        $this->connection->table('Program')->insert($this->program->toArrayForDbEntry());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
    }

}

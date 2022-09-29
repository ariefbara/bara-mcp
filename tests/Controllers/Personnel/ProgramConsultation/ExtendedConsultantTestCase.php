<?php

namespace Tests\Controllers\Personnel\ProgramConsultation;

use Tests\Controllers\Personnel\PersonnelTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;

class ExtendedConsultantTestCase extends PersonnelTestCase
{

    /**
     * 
     * @var RecordOfConsultant
     */
    protected $consultant;
    protected $consultantUri;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Consultant')->truncate();

        $program = new RecordOfProgram($this->personnel->firm, '99');
        $this->consultant = new RecordOfConsultant($program, $this->personnel, '99');

        $this->consultantUri = $this->personnelUri . "/consultants/{$this->consultant->id}";
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Consultant')->truncate();
    }

    protected function persistConsultantDependency()
    {
        $this->consultant->program->insert($this->connection);
        $this->consultant->insert($this->connection);
    }

}

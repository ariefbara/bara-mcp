<?php

namespace Tests\Controllers\Personnel\ProgramConsultant;

use Tests\Controllers\{
    Personnel\PersonnelTestCase,
    RecordPreparation\Firm\Program\RecordOfConsultant,
    RecordPreparation\Firm\RecordOfProgram
};

class ProgramConsultantTestCase extends PersonnelTestCase
{

    protected $programConsultantUri;

    /**
     *
     * @var RecordOfConsultant
     */
    protected $programConsultant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table("Program")->truncate();
        $this->connection->table("Consultant")->truncate();
        
        $program = new RecordOfProgram($this->personnel->firm, 0);
        $this->connection->table("Program")->insert($program->toArrayForDbEntry());
        
        $this->programConsultant = new RecordOfConsultant($program, $this->personnel, 0);
        $this->connection->table("Consultant")->insert($this->programConsultant->toArrayForDbEntry());
        
        $this->programConsultantUri = $this->personnelUri . "/program-consultant/{$this->programConsultant->id}";
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Program")->truncate();
        $this->connection->table("Consultant")->truncate();
    }

}

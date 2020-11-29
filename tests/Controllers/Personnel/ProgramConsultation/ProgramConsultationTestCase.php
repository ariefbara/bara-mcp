<?php

namespace Tests\Controllers\Personnel\ProgramConsultation;

use Tests\Controllers\{
    Personnel\PersonnelTestCase,
    RecordPreparation\Firm\Program\RecordOfConsultant,
    RecordPreparation\Firm\RecordOfProgram
};

class ProgramConsultationTestCase extends PersonnelTestCase
{

    protected $programConsultationUri;

    /**
     *
     * @var RecordOfConsultant
     */
    protected $programConsultation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table("Program")->truncate();
        $this->connection->table("Consultant")->truncate();
        
        $program = new RecordOfProgram($this->personnel->firm, "main");
        $this->connection->table("Program")->insert($program->toArrayForDbEntry());
        
        $this->programConsultation = new RecordOfConsultant($program, $this->personnel, 'main');
        $this->connection->table("Consultant")->insert($this->programConsultation->toArrayForDbEntry());
        
        $this->programConsultationUri = $this->personnelUri . "/program-consultations/{$this->programConsultation->id}";
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Program")->truncate();
        $this->connection->table("Consultant")->truncate();
    }
    
    protected function removeProgramConsultation()
    {
        $this->connection->table("Consultant")->truncate();
        $this->programConsultation->active = false;
        $this->connection->table("Consultant")->insert($this->programConsultation->toArrayForDbEntry());
    }

}

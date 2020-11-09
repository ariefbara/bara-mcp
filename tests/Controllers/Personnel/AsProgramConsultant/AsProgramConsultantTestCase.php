<?php

namespace Tests\Controllers\Personnel\AsProgramConsultant;

use Tests\Controllers\ {
    Personnel\PersonnelTestCase,
    RecordPreparation\Firm\Program\RecordOfConsultant,
    RecordPreparation\Firm\RecordOfPersonnel,
    RecordPreparation\Firm\RecordOfProgram
};

class AsProgramConsultantTestCase extends PersonnelTestCase
{
    protected $asProgramConsultantUri;
    /**
     *
     * @var RecordOfConsultant
     */
    protected $consultant;
    /**
     *
     * @var RecordOfConsultant
     */
    protected $removedConsultant;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Consultant')->truncate();
        
        $program = new RecordOfProgram($this->personnel->firm, 999);
        $this->connection->table('Program')->insert($program->toArrayForDbEntry());
        
        $personnel = new RecordOfPersonnel($this->personnel->firm, 99, 'removedPersonnel@email.org', 'password123');
        $this->connection->table('Personnel')->insert($personnel->toArrayForDbEntry());
        
        $this->consultant = new RecordOfConsultant($program, $this->personnel, 999);
        $this->removedConsultant = new RecordOfConsultant($program, $personnel, 998);
        $this->removedConsultant->removed = true;
        $this->connection->table('Consultant')->insert($this->consultant->toArrayForDbEntry());
        $this->connection->table('Consultant')->insert($this->removedConsultant->toArrayForDbEntry());
        
        $this->asProgramConsultantUri = $this->personnelUri . "/as-program-consultant/{$program->id}";
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Consultant')->truncate();
    }
}

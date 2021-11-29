<?php

namespace Tests\Controllers\Personnel;

use Tests\Controllers\ControllerTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\RecordOfFirm;

class PersonnelTestCase extends ControllerTestCase
{

    protected $personnelUri = "/api/personnel";

    /**
     *
     * @var RecordOfPersonnel
     */
    protected $personnel;

    /**
     *
     * @var RecordOfPersonnel
     */
    protected $removedPersonnel;
    
    /**
     * 
     * @var RecordOfConsultant
     */
    protected $mentor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Consultant')->truncate();

        $firm = new RecordOfFirm(999, 'firm_identifier');
        $this->connection->table('Firm')->insert($firm->toArrayForDbEntry());

        $this->personnel = new RecordOfPersonnel($firm, 999);
        $this->removedPersonnel = new RecordOfPersonnel($firm, 'removed');
        $this->removedPersonnel->active = false;
        $this->connection->table('Personnel')->insert($this->personnel->toArrayForDbEntry());
        $this->connection->table('Personnel')->insert($this->removedPersonnel->toArrayForDbEntry());
        
        $program = new RecordOfProgram($firm, '999');
        $this->mentor = new RecordOfConsultant($program, $this->personnel, '999');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Consultant')->truncate();
    }
    
    protected function insertMentorDependency(): void
    {
        $this->mentor->program->insert($this->connection);
        $this->mentor->insert($this->connection);
    }

}

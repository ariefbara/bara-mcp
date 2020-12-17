<?php

namespace Tests\Controllers\User\AsProgramRegistrant;

use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfRegistrant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\RecordOfFirm;
use Tests\Controllers\RecordPreparation\User\RecordOfUserRegistrant;
use Tests\Controllers\User\UserTestCase;

class AsProgramRegistrantTestCase extends UserTestCase
{
    protected $asProgramRegistrantUri;
    /**
     * 
     * @var RecordOfUserRegistrant
     */
    protected $programRegistration;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->connection->table("Firm")->truncate();
        $this->connection->table("Program")->truncate();
        $this->connection->table("Registrant")->truncate();
        $this->connection->table("UserRegistrant")->truncate();
        
        $firm = new RecordOfFirm(999);
        $this->connection->table("Firm")->insert($firm->toArrayForDbEntry());
        
        $program = new RecordOfProgram($firm, 999);
        $this->connection->table("Program")->insert($program->toArrayForDbEntry());
        
        $registrant = new RecordOfRegistrant($program, 999);
        $this->connection->table("Registrant")->insert($registrant->toArrayForDbEntry());
        
        $this->programRegistration = new RecordOfUserRegistrant($this->user, $registrant);
        $this->connection->table("UserRegistrant")->insert($this->programRegistration->toArrayForDbEntry());
        
        $this->asProgramRegistrantUri = $this->userUri . "/as-program-registrant/{$firm->id}/{$program->id}";
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Program")->truncate();
        $this->connection->table("Registrant")->truncate();
        $this->connection->table("UserRegistrant")->truncate();
    }
    
    protected function setRegistrationConcluded()
    {
        $this->programRegistration->registrant->concluded = true;
        $this->connection->table("Registrant")->truncate();
        $this->connection->table("Registrant")->insert($this->programRegistration->registrant->toArrayForDbEntry());
    }
}

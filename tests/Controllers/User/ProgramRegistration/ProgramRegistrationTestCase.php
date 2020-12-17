<?php

namespace Tests\Controllers\User\ProgramRegistration;

use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfRegistrant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\RecordOfFirm;
use Tests\Controllers\RecordPreparation\User\RecordOfUserRegistrant;
use Tests\Controllers\User\UserTestCase;

class ProgramRegistrationTestCase extends UserTestCase
{
    protected $programRegistrationUri;
    /**
     * 
     * @var RecordOfUserRegistrant
     */
    protected $programRegistration;
    /**
     * 
     * @var RecordOfUserRegistrant
     */
    protected $concludedProgramRegistration;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programRegistrationUri = $this->userUri . "/program-registrations";
        
        $this->connection->table("Firm")->truncate();
        $this->connection->table("Program")->truncate();
        $this->connection->table("Registrant")->truncate();
        $this->connection->table("UserRegistrant")->truncate();
        
        $firm = new RecordOfFirm(999);
        $this->connection->table("Firm")->insert($firm->toArrayForDbEntry());
        
        $program = new RecordOfProgram($firm, 999);
        $this->connection->table("Program")->insert($program->toArrayForDbEntry());
        
        $registrant = new RecordOfRegistrant($program, 999);
        $concludedRegistrant = new RecordOfRegistrant($program, 888);
        $concludedRegistrant->concluded = true;
        $this->connection->table("Registrant")->insert($registrant->toArrayForDbEntry());
        $this->connection->table("Registrant")->insert($concludedRegistrant->toArrayForDbEntry());
        
        $this->programRegistration = new RecordOfUserRegistrant($this->user, $registrant);
        $this->concludedProgramRegistration = new RecordOfUserRegistrant($this->user, $concludedRegistrant);
        $this->connection->table("UserRegistrant")->insert($this->programRegistration->toArrayForDbEntry());
        $this->connection->table("UserRegistrant")->insert($this->concludedProgramRegistration->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Program")->truncate();
        $this->connection->table("Registrant")->truncate();
        $this->connection->table("UserRegistrant")->truncate();
    }
}

<?php

namespace Tests\Controllers\Client\ProgramRegistration;

use Tests\Controllers\Client\ClientTestCase;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientRegistrant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfRegistrant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;

class ProgramRegistrationTestCase extends ClientTestCase
{
    protected $programRegistrationUri;
    /**
     * 
     * @var RecordOfClientRegistrant
     */
    protected $programRegistration;
    /**
     * 
     * @var RecordOfClientRegistrant
     */
    protected $concludedProgramRegistration;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programRegistrationUri = $this->clientUri . "/program-registrations";
        
        $this->connection->table("Program")->truncate();
        $this->connection->table("Registrant")->truncate();
        $this->connection->table("ClientRegistrant")->truncate();
        
        $firm = $this->client->firm;
        
        $program = new RecordOfProgram($firm, 999);
        $this->connection->table("Program")->insert($program->toArrayForDbEntry());
        
        $registrant = new RecordOfRegistrant($program, 999);
        $concludedRegistrant = new RecordOfRegistrant($program, 888);
        $concludedRegistrant->concluded = true;
        $this->connection->table("Registrant")->insert($registrant->toArrayForDbEntry());
        $this->connection->table("Registrant")->insert($concludedRegistrant->toArrayForDbEntry());
        
        $this->programRegistration = new RecordOfClientRegistrant($this->client, $registrant);
        $this->concludedProgramRegistration = new RecordOfClientRegistrant($this->client, $concludedRegistrant);
        $this->connection->table("ClientRegistrant")->insert($this->programRegistration->toArrayForDbEntry());
        $this->connection->table("ClientRegistrant")->insert($this->concludedProgramRegistration->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Program")->truncate();
        $this->connection->table("Registrant")->truncate();
        $this->connection->table("ClientRegistrant")->truncate();
    }
}

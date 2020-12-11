<?php

namespace Tests\Controllers\Client\AsProgramRegistrant;

use Tests\Controllers\Client\ClientTestCase;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientRegistrant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfRegistrant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;

class AsProgramRegistrantTestCase extends ClientTestCase
{
    protected $asProgramRegistrantUri;
    /**
     * 
     * @var RecordOfClientRegistrant
     */
    protected $programRegistration;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->connection->table("Program")->truncate();
        $this->connection->table("Registrant")->truncate();
        $this->connection->table("ClientRegistrant")->truncate();
        
        $firm = $this->client->firm;
        
        $program = new RecordOfProgram($firm, 999);
        $this->connection->table("Program")->insert($program->toArrayForDbEntry());
        
        $registrant = new RecordOfRegistrant($program, 999);
        $this->connection->table("Registrant")->insert($registrant->toArrayForDbEntry());
        
        $this->programRegistration = new RecordOfClientRegistrant($this->client, $registrant);
        $this->connection->table("ClientRegistrant")->insert($this->programRegistration->toArrayForDbEntry());
        
        $this->asProgramRegistrantUri = $this->clientUri . "/as-program-registrant/{$program->id}";
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Program")->truncate();
        $this->connection->table("Registrant")->truncate();
        $this->connection->table("ClientRegistrant")->truncate();
    }
    
    protected function setRegistrationConcluded()
    {
        $this->programRegistration->registrant->concluded = true;
        $this->connection->table("Registrant")->truncate();
        $this->connection->table("Registrant")->insert($this->programRegistration->registrant->toArrayForDbEntry());
    }
}

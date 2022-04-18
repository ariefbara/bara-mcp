<?php

namespace Firm\Domain\Event;

use Config\EventList;
use SharedContext\Domain\ValueObject\RegistrationStatus;
use Tests\TestBase;

class ProgramRegistrationReceivedTest extends TestBase
{
    protected $registrantId = 'registrant-id';
    protected $registrationStatus;
    protected $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registrationStatus = $this->buildMockOfClass(RegistrationStatus::class);
        $this->event = new TestableProgramRegistrationReceived('id', $this->registrationStatus);
    }
    
    protected function construct()
    {
        return new TestableProgramRegistrationReceived($this->registrantId, $this->registrationStatus);
    }
    public function test_construct_setProperties()
    {
        $event = $this->construct();
        $this->assertSame($this->registrantId, $event->registrantId);
        $this->assertSame($this->registrationStatus, $event->registrationStatus);
    }
    
    public function test_getName_returnEventName()
    {
        $this->assertSame(EventList::PROGRAM_REGISTRATION_RECEIVED, $this->event->getName());
    }
}

class TestableProgramRegistrationReceived extends ProgramRegistrationReceived
{
    public $registrantId;
    public $registrationStatus;
}

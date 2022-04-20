<?php

namespace Client\Domain\Model\Client;

use Client\Domain\DependencyModel\Firm\Program;
use Client\Domain\DependencyModel\Firm\Program\Registrant;
use Client\Domain\Model\Client;
use Config\EventList;
use Resources\Domain\Event\CommonEvent;
use Tests\TestBase;

class ClientRegistrantTest extends TestBase
{
    protected $client;
    protected $registrant;
    protected $clientRegistrant;
    protected $id = 'client-registrant-id';
    protected $program;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->registrant = $this->buildMockOfClass(Registrant::class);
        $this->clientRegistrant = new TestableClientRegistrant($this->client, 'id', $this->registrant);
        
        $this->program = $this->buildMockOfClass(Program::class);
    }
    
    protected function construct()
    {
        return new TestableClientRegistrant($this->client, $this->id, $this->registrant);
    }
    public function test_construct_setProperties()
    {
        $clientRegistrant = $this->construct();
        $this->assertSame($this->client, $clientRegistrant->client);
        $this->assertSame($this->id, $clientRegistrant->id);
        $this->assertSame($this->registrant, $clientRegistrant->registrant);
    }
    public function test_construct_recordClientRegistrantAddedEvent()
    {
        $event = new CommonEvent(EventList::CLIENT_REGISTRANT_CREATED, $this->id);
        $clientRegistrant = $this->construct();
        $this->assertEquals($event, $clientRegistrant->recordedEvents[0]);
    }
    
    protected function isActiveRegistrationCorrespondWithProgram()
    {
        return $this->clientRegistrant->isActiveRegistrationCorrespondWithProgram($this->program);
    }
    public function test_isActiveRegistrationCorrespondWithProgram_returnRegistrantInspectionResult()
    {
        $this->registrant->expects($this->once())
                ->method('isActiveRegistrationCorrespondWithProgram')
                ->with($this->program)
                ->willReturn(true);
        $this->assertTrue($this->isActiveRegistrationCorrespondWithProgram());
    }
}

class TestableClientRegistrant extends ClientRegistrant
{
    public $client;
    public $id;
    public $registrant;
    public $recordedEvents;
}

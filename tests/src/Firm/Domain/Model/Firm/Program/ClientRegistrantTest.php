<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\ {
    Client,
    Program
};
use Tests\TestBase;

class ClientRegistrantTest extends TestBase
{
    protected $clientRegistrant, $registrant;
    
    protected $program;
    protected $client;
    protected $id = 'newClientRegistrantId';
    
    protected $clientParticipantId = 'newClientParticipantId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->client = $this->buildMockOfClass(Client::class);
        
        $this->clientRegistrant = new TestableClientRegistrant();
        $this->clientRegistrant->program = $this->program;
        $this->clientRegistrant->client = $this->client;
        
        $this->registrant = $this->buildMockOfClass(Registrant::class);
        $this->clientRegistrant->registrant = $this->registrant;
    }
    
    public function test_accept_executeRegistrantAcceptMethod()
    {
        $this->registrant->expects($this->once())
                ->method('accept');
        $this->clientRegistrant->accept();
    }
    
    public function test_createParticipant_returnClientParticipant()
    {
        $clientParticipant = new ClientParticipant($this->program, $this->clientParticipantId, $this->client);
        $this->assertEquals($clientParticipant, $this->clientRegistrant->createParticipant($this->clientParticipantId));
    }
    
    public function test_reject_executeRegistrantsRejectMethod()
    {
        $this->registrant->expects($this->once())
                ->method('reject');
        $this->clientRegistrant->reject();
    }
    
    public function test_clientEquals_sameClient_returnTrue()
    {
        $this->assertTrue($this->clientRegistrant->clientEquals($this->client));
    }
    public function test_clientEquals_differenetClient_returnFalse()
    {
        $client = $this->buildMockOfClass(Client::class);
        $this->assertFalse($this->clientRegistrant->clientEquals($client));
    }
    
}

class TestableClientRegistrant extends ClientRegistrant
{
    public $program;
    public $id;
    public $client;
    public $registrant;
    
    function __construct()
    {
        parent::__construct();
    }
}

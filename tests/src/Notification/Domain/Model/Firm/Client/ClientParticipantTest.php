<?php

namespace Notification\Domain\Model\Firm\Client;

use Notification\Domain\Model\Firm\Client;
use Tests\TestBase;

class ClientParticipantTest extends TestBase
{
    protected $clientParticipant;
    protected $client;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->clientParticipant = new TestableClientParticipant();
        
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientParticipant->client = $this->client;
    }
    
    public function test_getClientName_returnClientGetNameResult()
    {
        $this->client->expects($this->once())
                ->method('getName');
        $this->clientParticipant->getClientName();
    }
}

class TestableClientParticipant extends ClientParticipant
{
    public $id;
    public $client;
    public $participant;
    
    function __construct()
    {
        parent::__construct();
    }
}

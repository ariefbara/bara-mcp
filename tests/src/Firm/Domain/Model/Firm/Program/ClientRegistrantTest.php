<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Model\Firm\Program;
use Tests\TestBase;

class ClientRegistrantTest extends TestBase
{
    protected $clientRegistrant;
    protected $client;
    protected $program;
    protected $participantId = 'participantId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRegistrant = new TestableClientRegistrant();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRegistrant->client = $this->client;
        
        $this->program = $this->buildMockOfClass(Program::class);
    }
    
    public function test_createParticipant_returnParticipant()
    {
        $participant = Participant::participantForClient(
                $this->program, $this->participantId, $this->clientRegistrant->client);
        
        $this->assertEquals($participant, $this->clientRegistrant->createParticipant($this->program, $this->participantId));
    }
    
    public function test_clientEquals_sameClient_returnTrue()
    {
        $this->assertTrue($this->clientRegistrant->clientEquals($this->client));
    }
    public function test_clientEquals_differentClient_returnFalse()
    {
        $client = $this->buildMockOfClass(Client::class);
        $this->assertFalse($this->clientRegistrant->clientEquals($client));
    }
/*    
    public function test_clientIdEquals_differentId_returnFalse()
    {
        $this->assertFalse($this->clientRegistrant->clientEquals('differentId'));
    }
 * 
 */
}

class TestableClientRegistrant extends ClientRegistrant
{
    public $registrant;
    public $id = 'registrant-id';
    public $client;
    
    function __construct()
    {
        parent::__construct();
    }
}

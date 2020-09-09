<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Program;
use Tests\TestBase;

class ClientRegistrantTest extends TestBase
{
    protected $clientRegistrant;
    
    protected $program;
    protected $participantId = 'participantId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRegistrant = new TestableClientRegistrant();
        $this->clientRegistrant->clientId = "clientId";
        
        $this->program = $this->buildMockOfClass(Program::class);
    }
    
    public function test_createParticipant_returnParticipant()
    {
        $participant = Participant::participantForClient(
                $this->program, $this->participantId, $this->clientRegistrant->clientId);
        
        $this->assertEquals($participant, $this->clientRegistrant->createParticipant($this->program, $this->participantId));
    }
    
    public function test_clientIdEquals_sameId_returnTrue()
    {
        $this->assertTrue($this->clientRegistrant->clientIdEquals($this->clientRegistrant->clientId));
    }
    public function test_clientIdEquals_differentId_returnFalse()
    {
        $this->assertFalse($this->clientRegistrant->clientIdEquals('differentId'));
    }
}

class TestableClientRegistrant extends ClientRegistrant
{
    public $registrant;
    public $id;
    public $clientId;
    
    function __construct()
    {
        parent::__construct();
    }
}

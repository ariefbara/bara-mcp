<?php

namespace Firm\Domain\Model\Firm\Client;

use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\Participant;
use Tests\TestBase;

class ClientParticipantTest extends TestBase
{
    protected $client;
    protected $participant;
    protected $clientParticipant;
    protected $id = 'newId';
    //
    protected $program;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->clientParticipant = new TestableClientParticipant($this->client, 'id', $this->participant);
        //
        $this->program = $this->buildMockOfClass(Program::class);
    }
    
    protected function construct()
    {
        return new TestableClientParticipant($this->client, $this->id, $this->participant);
    }
    public function test_construct_setProperties()
    {
        $clientParticipant = $this->construct();
        $this->assertSame($this->client, $clientParticipant->client);
        $this->assertSame($this->id, $clientParticipant->id);
        $this->assertSame($this->participant, $clientParticipant->participant);
    }
    
    protected function isActiveParticipantOrRegistrantOfProgram()
    {
        return $this->clientParticipant->isActiveParticipantOrRegistrantOfProgram($this->program);
    }
    public function test_isActiveParticipantOrRegistrantOfProgram_returnParticipantComparisonResult()
    {
        $this->participant->expects($this->once())
                ->method('isActiveParticipantOrRegistrantOfProgram')
                ->with($this->program);
        $this->isActiveParticipantOrRegistrantOfProgram();
    }
}

class TestableClientParticipant extends ClientParticipant
{
    public $client;
    public $id;
    public $participant;
}

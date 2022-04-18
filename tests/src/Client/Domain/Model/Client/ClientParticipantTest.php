<?php

namespace Client\Domain\Model\Client;

use Client\Domain\DependencyModel\Firm\Program\Participant;
use Client\Domain\Model\Client;
use Tests\TestBase;

class ClientParticipantTest extends TestBase
{
    protected $client;
    protected $participant;
    protected $clientParticipant;
    protected $id = 'client-participant-id';
    protected $program;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->clientParticipant = new TestableClientParticipant($this->client, 'id', $this->participant);
        
        $this->program = $this->buildMockOfClass(\Client\Domain\DependencyModel\Firm\Program::class);
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
    
    //
    protected function isActiveParticipationCorrespondWithProgram()
    {
        return $this->clientParticipant->isActiveParticipationCorrespondWithProgram($this->program);
    }
    public function test_isActiveParticipationCorrespondWithProgram_returnParticipantInspectionResult()
    {
        $this->participant->expects($this->once())
                ->method('isActiveParticipationCorrespondWithProgram')
                ->with($this->program)
                ->willReturn(true);
        $this->assertTrue($this->isActiveParticipationCorrespondWithProgram());
    }
}

class TestableClientParticipant extends ClientParticipant
{
    public $client;
    public $id;
    public $participant;
}

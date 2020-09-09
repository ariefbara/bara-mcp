<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\ {
    Client,
    Program
};
use Resources\ {
    Application\Service\Mailer,
    Domain\ValueObject\PersonName
};
use Tests\TestBase;

class ClientParticipantTest extends TestBase
{
    protected $clientParticipant, $participant;
    
    protected $id = 'newClientParticipantId', $clientId = 'clientId';
    
    protected $registrant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        
        $this->clientParticipant = new TestableClientParticipant($this->participant, 'id', 'clientId');
        
        $this->registrant = $this->buildMockOfClass(Registrant::class);
    }
    
    public function test_construct_setProperties()
    {
        $clientParticipant = new TestableClientParticipant($this->participant, $this->id, $this->clientId);
        $this->assertEquals($this->participant, $clientParticipant->participant);
        $this->assertEquals($this->id, $clientParticipant->id);
        $this->assertEquals($this->clientId, $clientParticipant->clientId);
    }
    
    public function test_correspondWithRegistrant_returnRegistrantsCorrespondWithClientResult()
    {
        $this->registrant->expects($this->once())
                ->method('correspondWithClient')
                ->with($this->clientId);
        
        $this->clientParticipant->correspondWithRegistrant($this->registrant);
    }
    
}

class TestableClientParticipant extends ClientParticipant
{
    public $participant;
    public $id;
    public $clientId;
}

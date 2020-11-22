<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Program\MeetingType\MeetingData;
use Tests\TestBase;

class ClientParticipantTest extends TestBase
{
    protected $clientParticipant, $participant;
    protected $id = 'newClientParticipantId', $clientId = 'clientId';
    protected $registrant;
    protected $meetingId = "meetingId", $meetingType, $meetingData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        
        $this->clientParticipant = new TestableClientParticipant($this->participant, 'id', 'clientId');
        
        $this->registrant = $this->buildMockOfClass(Registrant::class);
        
        $this->meetingType = $this->buildMockOfClass(ActivityType::class);
        $this->meetingData = $this->buildMockOfClass(MeetingData::class);
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
    
    public function test_initiateMeeting_returnParticipantInitiateMeetingResult()
    {
        $this->participant->expects($this->once())
                ->method("initiateMeeting")
                ->with($this->meetingId, $this->meetingType, $this->meetingData);
        $this->clientParticipant->initiateMeeting($this->meetingId, $this->meetingType, $this->meetingData);
    }
    
}

class TestableClientParticipant extends ClientParticipant
{
    public $participant;
    public $id;
    public $clientId;
}

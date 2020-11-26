<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Program\MeetingType\MeetingData;
use Tests\TestBase;

class UserParticipantTest extends TestBase
{
    protected $userParticipant;
    protected $participant;
    protected $id = 'id', $userId = 'userId';
    protected $registrant;
    protected $meetingId = "meetingId", $meetingType, $meetingData;

    protected function setUp(): void
    {
        parent::setUp();
        
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->userParticipant = new TestableUserParticipant($this->participant, 'id', 'userId');
        
        $this->registrant = $this->buildMockOfClass(Registrant::class);
        $this->meetingType = $this->buildMockOfClass(ActivityType::class);
        $this->meetingData = $this->buildMockOfClass(MeetingData::class);
    }
    
    public function test_construct_setProperties()
    {
        $userParticipant = new TestableUserParticipant($this->participant, $this->id, $this->userId);
        $this->assertEquals($this->participant, $userParticipant->participant);
        $this->assertEquals($this->id, $userParticipant->id);
        $this->assertEquals($this->userId, $userParticipant->userId);
    }
    
    public function test_correspondWithRegistrant_returnRegistrantsCorrespondWithUserResult()
    {
        $this->registrant->expects($this->once())
                ->method('correspondWithUser')
                ->with($this->userParticipant->userId);
        $this->userParticipant->correspondWithRegistrant($this->registrant);
    }
    
    public function test_initiateMeeting_returnParticipantInitiateMeetingResult()
    {
        $this->participant->expects($this->once())
                ->method("initiateMeeting")
                ->with($this->meetingId, $this->meetingType, $this->meetingData);
        $this->userParticipant->initiateMeeting($this->meetingId, $this->meetingType, $this->meetingData);
    }
    
}

class TestableUserParticipant extends UserParticipant
{
    public $participant;
    public $id;
    public $userId;
}

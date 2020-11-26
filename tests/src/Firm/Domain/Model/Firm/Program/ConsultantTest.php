<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\ {
    Personnel,
    Program,
    Program\MeetingType\Meeting\Attendee,
    Program\MeetingType\MeetingData
};
use SharedContext\Domain\ValueObject\ActivityParticipantType;
use Tests\TestBase;

class ConsultantTest extends TestBase
{

    protected $program;
    protected $id = 'consultant-id';
    protected $personnel;
    protected $consultant;
    protected $meetingId = "meetingId", $meetingType, $meetingData;
    protected $attendee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->consultant = new TestableConsultant($this->program, 'id', $this->personnel);
        
        $this->attendee = $this->buildMockOfClass(Attendee::class);
        
        $this->meetingType = $this->buildMockOfClass(ActivityType::class);
        $this->meetingData = $this->buildMockOfClass(MeetingData::class);
    }

    public function test_construct_setProperties()
    {
        $consultant = new TestableConsultant($this->program, $this->id, $this->personnel);
        $this->assertEquals($this->program, $consultant->program);
        $this->assertEquals($this->id, $consultant->id);
        $this->assertEquals($this->personnel, $consultant->personnel);
        $this->assertFalse($consultant->removed);
    }

    public function test_remove_setRemovedFlagTrue()
    {
        $this->consultant->remove();
        $this->assertTrue($this->consultant->removed);
    }

    public function test_reassign_setRemovedFlagFalse()
    {
        $this->consultant->removed = true;
        $this->consultant->reassign();
        $this->assertFalse($this->consultant->removed);
    }
    
    public function test_getPersonnelName_returnPersonnelsGetNameResult()
    {
        $this->personnel->expects($this->once())
                ->method('getName')
                ->willReturn($name = 'hadi pranoto');
        $this->assertEquals($name, $this->consultant->getPersonnelName());
    }
    
    public function test_canInvolvedInProgram_sameProgram_returnTrue()
    {
        $this->assertTrue($this->consultant->canInvolvedInProgram($this->consultant->program));
    }
    public function test_canInvolvedInProgram_differentProgram_returnFalse()
    {
        $program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->consultant->canInvolvedInProgram($program));
    }
    public function test_canInvolvedInProgram_inactiveConsultant_returnFalse()
    {
        $this->consultant->removed = true;
        $this->assertFalse($this->consultant->canInvolvedInProgram($this->consultant->program));
    }
    
    public function test_roleCorrespondWith_returnActivityParticipantTypeIsConsultantResult()
    {
        $activityParticipantType = $this->buildMockOfClass(ActivityParticipantType::class);
        $activityParticipantType->expects($this->once())
                ->method("isConsultantType");
        $this->consultant->roleCorrespondWith($activityParticipantType);
    }
    
    protected function executeRegisterAsAttendeeCandidate()
    {
        $this->consultant->registerAsAttendeeCandidate($this->attendee);
    }
    public function test_registerAsAttendeeCandidate_setConsultantAsAttendeeCandidate()
    {
        $this->attendee->expects($this->once())
                ->method("setConsultantAsAttendeeCandidate")
                ->with($this->consultant);
        $this->executeRegisterAsAttendeeCandidate();
    }
    
    protected function executeInitiateMeeting()
    {
        $this->meetingType->expects($this->any())
                ->method("belongsToProgram")
                ->willReturn(true);
        return $this->consultant->initiateMeeting($this->meetingId, $this->meetingType, $this->meetingData);
    }
    public function test_initiateMeeting_returnMeetingTypeCreateMeetingResult()
    {
        $this->meetingType->expects($this->once())
                ->method("createMeeting")
                ->with($this->meetingId, $this->meetingData, $this->consultant);
        $this->executeInitiateMeeting();
    }
    public function test_initiateMeeting_inactiveConsultant_forbidden()
    {
        $this->consultant->removed = true;
        $operation = function (){
            $this->executeInitiateMeeting();
        };
        $errorDetail = "forbidden: only active consultant can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_initiateMeeting_meetingTypeNotFromInProgram_forbidden()
    {
        $this->meetingType->expects($this->once())
                ->method("belongsToProgram")
                ->with($this->program)
                ->willReturn(false);
        $operation = function (){
            $this->executeInitiateMeeting();
        };
        $errorDetail = "forbidden: can only manage meeting type from same program";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }

}

class TestableConsultant extends Consultant
{

    public $program, $id, $personnel, $removed;

}

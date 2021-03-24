<?php

namespace Firm\Domain\Model\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\ {
    Firm,
    Firm\Personnel,
    Firm\Program,
    Firm\Program\ConsultationSetup\ConsultationRequest,
    Firm\Program\ConsultationSetup\ConsultationSession,
    Firm\Program\MeetingType\Meeting\Attendee,
    Firm\Program\MeetingType\Meeting\Attendee\ConsultantAttendee,
    Firm\Program\MeetingType\MeetingData
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
    protected $meetingInvitation;
    protected $consultationRequest;
    protected $consultationSession;
    protected $firm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->personnel->expects($this->any())->method("isActive")->willReturn(true);
        
        $this->consultant = new TestableConsultant($this->program, 'id', $this->personnel);
        $this->consultant->meetingInvitations = new ArrayCollection();
        $this->consultant->consultationRequests = new ArrayCollection();
        $this->consultant->consultationSessions = new ArrayCollection();
        
        $this->meetingInvitation = $this->buildMockOfClass(ConsultantAttendee::class);
        $this->consultant->meetingInvitations->add($this->meetingInvitation);
        
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->consultant->consultationRequests->add($this->consultationRequest);
        
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->consultant->consultationSessions->add($this->consultationSession);
        
        $this->attendee = $this->buildMockOfClass(Attendee::class);
        
        $this->meetingType = $this->buildMockOfClass(ActivityType::class);
        $this->meetingData = $this->buildMockOfClass(MeetingData::class);
        
        $this->firm = $this->buildMockOfClass(Firm::class);
    }

    public function test_construct_setProperties()
    {
        $consultant = new TestableConsultant($this->program, $this->id, $this->personnel);
        $this->assertEquals($this->program, $consultant->program);
        $this->assertEquals($this->id, $consultant->id);
        $this->assertEquals($this->personnel, $consultant->personnel);
        $this->assertTrue($consultant->active);
    }
    public function test_construct_inactivePersonnel_forbidden()
    {
        $operation = function (){
            $personnel = $this->buildMockOfClass(Personnel::class);
            $personnel->expects($this->once())->method("isActive")->willReturn(false);
            new TestableConsultant($this->program, $this->id, $personnel);
        };
        $errorDetail = "forbidden: can only assign active personnel as program mentor";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    public function test_belongsToProgram_sameProgram_returnTrue()
    {
        $this->assertTrue($this->consultant->belongsToProgram($this->consultant->program));
    }
    public function test_belongsToProgram_differentProgram_returnFalse()
    {
        $program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->consultant->belongsToProgram($program));
    }

    protected function executeDisable()
    {
        $this->consultant->disable();
    }
    public function test_disable_setInactive()
    {
        $this->executeDisable();
        $this->assertFalse($this->consultant->active);
    }
    public function test_disable_disableAllValidInvitation()
    {
        $this->meetingInvitation->expects($this->once())
                ->method("disableValidInvitation");
        $this->executeDisable();
    }
    public function test_disable_disableUpcomingRequest()
    {
        $this->consultationRequest->expects($this->once())
                ->method("disableUpcomingRequest");
        $this->executeDisable();
    }
    public function test_disable_disableUpcomingSession()
    {
        $this->consultationSession->expects($this->once())
                ->method("disableUpcomingSession");
        $this->executeDisable();
    }

    public function test_enable_setRemovedFlagFalse()
    {
        $this->consultant->active = false;
        $this->consultant->enable();
        $this->assertTrue($this->consultant->active);
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
        $this->consultant->active = false;
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
    public function test_registerAsAttendeeCandidate_inactiveConsultant_forbidden()
    {
        $this->consultant->active = false;
        $operation = function (){
            $this->executeRegisterAsAttendeeCandidate();
        };
        $errorDetail = "forbidden: can only invite active consultant to meeting";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
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
        $this->consultant->active = false;
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
    
    public function test_belongsToFirm_returnProgramsBelongsToFirmResult()
    {
        $this->program->expects($this->once())
                ->method("belongsToFirm")
                ->with($this->firm);
        $this->consultant->belongsToFirm($this->firm);
    }

}

class TestableConsultant extends Consultant
{

    public $program, $id, $personnel, $active;
    public $meetingInvitations;
    public $consultationRequests;
    public $consultationSessions;

}

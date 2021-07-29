<?php

namespace Firm\Domain\Model\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Personnel;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\Attendee;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingData;
use Firm\Domain\Model\Firm\Program\Consultant\ConsultantAttendee;
use Firm\Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest;
use Firm\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession;
use Firm\Domain\Model\Firm\Program\Mission\MissionComment;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;
use Firm\Domain\Model\Firm\Program\Participant\DedicatedMentor;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class ConsultantTest extends TestBase
{

    protected $program;
    protected $id = 'consultant-id';
    protected $personnel;
    protected $consultant;
    protected $attendee;
    protected $meetingInvitation;
    protected $consultationRequest;
    protected $consultationSession;
    protected $firm;
    protected $mission, $missionComment, $missionCommentId = 'missionCommentId', $missionCommentData;
    
    protected $meetingId = "meetingId", $meetingType, $meetingData;
    protected $meeting;
    protected $dedicatedMentee;

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
        
        $this->firm = $this->buildMockOfClass(Firm::class);
        
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->missionComment = $this->buildMockOfClass(MissionComment::class);
        $this->missionCommentData = $this->buildMockOfClass(MissionCommentData::class);
        
        $this->meetingType = $this->buildMockOfClass(ActivityType::class);
        $this->meetingData = $this->buildMockOfClass(MeetingData::class);
        $this->meeting = $this->buildMockOfClass(Meeting::class);
        
        $this->dedicatedMentee = $this->buildMockOfClass(DedicatedMentor::class);
        $this->consultant->dedicatedMentees = new ArrayCollection();
        $this->consultant->dedicatedMentees->add($this->dedicatedMentee);
    }
    protected function assertInactiveConsultant(callable $operations): void
    {
        $this->assertRegularExceptionThrowed($operations, 'Forbidden', 'forbidden: only active consultant can make this request');
    }
    protected function assertInacessibleAsset(callable $operations, string $assetName): void
    {
        $this->assertRegularExceptionThrowed($operations, 'Forbidden', "forbidden: unable to access $assetName");
    }
    protected function setAccessibleAsset(MockObject $asset): void
    {
        $asset->expects($this->any())
                ->method('belongsToProgram')
                ->with($this->program)
                ->willReturn(true);
    }
    protected function setInaccessibleAsset(MockObject $asset): void
    {
        $asset->expects($this->any())
                ->method('belongsToProgram')
                ->with($this->program)
                ->willReturn(false);
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
    
    public function test_belongsToFirm_returnProgramsBelongsToFirmResult()
    {
        $this->program->expects($this->once())
                ->method("belongsToFirm")
                ->with($this->firm);
        $this->consultant->belongsToFirm($this->firm);
    }
    
    protected function executeSubmitCommentInMission()
    {
        $this->setAccessibleAsset($this->mission);
        $this->consultant->submitCommentInMission($this->mission, $this->missionCommentId, $this->missionCommentData);
    }
    public function test_submitCommentInMission_returnClientsSubmitCommentInMissionResult()
    {
        $this->personnel->expects($this->once())
                ->method('submitCommentInMission')
                ->with($this->mission, $this->missionCommentId, $this->missionCommentData);
        $this->executeSubmitCommentInMission();
    }
    public function test_submitCommentInMission_modifyCommentDataRolePaths()
    {
        $this->missionCommentData->expects($this->once())
                ->method('addRolePath')
                ->with('mentor', $this->consultant->id);
        $this->executeSubmitCommentInMission();
    }
    public function test_submitCommentInMission_inactiveConsultant_forbidden()
    {
        $this->consultant->active = false;
        $this->assertInactiveConsultant(function(){
            $this->executeSubmitCommentInMission();
        });
    }
    public function test_submitCommentInMission_inacessibleMission_forbidden()
    {
        $this->setInaccessibleAsset($this->mission);
        $this->assertInacessibleAsset(function (){
            $this->executeSubmitCommentInMission();
        }, 'mission');
    }
    
    protected function executeReplyMissionComment()
    {
        $this->setAccessibleAsset($this->missionComment);
        $this->consultant->replyMissionComment($this->missionComment, $this->missionCommentId, $this->missionCommentData);
    }
    public function test_replyMissionComment_returnClientsReplyMissionCommentResult()
    {
        $this->personnel->expects($this->once())
                ->method('replyMissionComment')
                ->with($this->missionComment, $this->missionCommentId, $this->missionCommentData);
        $this->executeReplyMissionComment();
    }
    public function test_replyMissionComment_modifyCommentDataRolePaths()
    {
        $this->missionCommentData->expects($this->once())
                ->method('addRolePath')
                ->with('mentor', $this->consultant->id);
        $this->executeReplyMissionComment();
    }
    public function test_replyMissionComment_inactiveConsultant_forbidden()
    {
        $this->consultant->active = false;
        $this->assertInactiveConsultant(function(){
            $this->executeReplyMissionComment();
        });
    }
    public function test_replyMissionComment_inacessibleMission_forbidden()
    {
        $this->setInaccessibleAsset($this->missionComment);
        $this->assertInacessibleAsset(function (){
            $this->executeReplyMissionComment();
        }, 'mission comment');
    }
    
    protected function executeInitiateMeeting()
    {
        return $this->consultant->initiateMeeting($this->meetingId, $this->meetingType, $this->meetingData);
    }
    public function test_initiateMeeting_returnMeetingCreatedInActivityType()
    {
        $this->meetingType->expects($this->once())
                ->method('createMeeting')
                ->with($this->meetingId, $this->meetingData)
                ->willReturn($meeting = $this->buildMockOfClass(Meeting::class));
        $this->assertEquals($meeting, $this->executeInitiateMeeting());
    }
    public function test_initiateMeeting_inactiveConsultant_forbidden()
    {
        $this->consultant->active = false;
        $this->assertInactiveConsultant(function (){
            $this->executeInitiateMeeting();
        });
    }
    public function test_initiateMeeting_assertMeetingTypeUsableInProgram()
    {
        $this->meetingType->expects($this->once())
                ->method('assertUsableInProgram')
                ->with($this->program);
        $this->executeInitiateMeeting();
    }
    public function test_initiateMeeting_aggregateConsultantAttendeeToMeetingInvitationCollection()
    {
        $this->executeInitiateMeeting();
        $this->assertEquals(2, $this->consultant->meetingInvitations->count());
        $this->assertInstanceOf(ConsultantAttendee::class, $this->consultant->meetingInvitations->last());
    }
    
    protected function executeInviteToMeeting()
    {
        $this->consultant->inviteToMeeting($this->meeting);
    }
    public function test_inviteToMeeting_addNewConsultantAttendeeToMeetingInvitationCollection()
    {
        $this->executeInviteToMeeting();
        $this->assertEquals(2, $this->consultant->meetingInvitations->count());
        $this->assertInstanceOf(ConsultantAttendee::class, $this->consultant->meetingInvitations->last());
    }
    public function test_inviteToMeeting_hasActiveInvitationToSameMeeting_void()
    {
        $this->meetingInvitation->expects($this->once())
                ->method('isActiveAttendeeOfMeeting')
                ->with($this->meeting)
                ->willReturn(true);
        $this->executeInviteToMeeting();
        $this->assertEquals(1, $this->consultant->meetingInvitations->count());
    }
    public function test_inviteToMeeting_inactiveConsultant_forbidden()
    {
        $this->consultant->active = false;
        $this->assertInactiveConsultant(function (){
            $this->executeInviteToMeeting();
        });
    }
    public function test_inviteToMeeting_assertMeetingUsableInProgram()
    {
        $this->meeting->expects($this->once())
                ->method('assertUsableInProgram')
                ->with($this->program);
        $this->executeInviteToMeeting();
    }
    
    protected function inviteAllActiveDedicatedMenteesToMeeting()
    {
        $this->dedicatedMentee->expects($this->any())
                ->method('isActiveAssignment')
                ->willReturn(true);
        $this->consultant->inviteAllActiveDedicatedMenteesToMeeting($this->meeting);
    }
    public function test_inviteAllActiveDedicatedMenteesToMeeting_inviteAllActiveDedicatedMenteesToMeeting()
    {
        $this->dedicatedMentee->expects($this->once())
                ->method('inviteParticipantToMeeting')
                ->with($this->meeting);
        $this->inviteAllActiveDedicatedMenteesToMeeting();
    }
    public function test_inviteAllActiveDedicatedMenteesToMeeting_containInactiveDedicatedMentee_skipInvitingInactiveMentee()
    {
        $this->dedicatedMentee->expects($this->once())
                ->method('isActiveAssignment')
                ->willReturn(false);
        $this->dedicatedMentee->expects($this->never())
                ->method('inviteParticipantToMeeting');
        $this->inviteAllActiveDedicatedMenteesToMeeting();
    }
    public function test_inviteAllActiveDedicatedMenteesToMeeting_recordMeeting()
    {
        $this->inviteAllActiveDedicatedMenteesToMeeting();
        $this->assertEquals($this->meeting, $this->consultant->aggregatedEntitiesHavingEvents[0]);
    }

}

class TestableConsultant extends Consultant
{

    public $program, $id = 'consultant-id', $personnel, $active;
    public $meetingInvitations;
    public $consultationRequests;
    public $consultationSessions;
    public $dedicatedMentees;
    public $aggregatedEntitiesHavingEvents;

}

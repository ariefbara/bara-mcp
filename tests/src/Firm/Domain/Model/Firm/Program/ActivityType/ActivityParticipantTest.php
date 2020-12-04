<?php

namespace Firm\Domain\Model\Firm\Program\ActivityType;

use Firm\Domain\ {
    Model\Firm\FeedbackForm,
    Model\Firm\Program\ActivityType,
    Model\Firm\Program\MeetingType\CanAttendMeeting,
    Model\Firm\Program\MeetingType\Meeting,
    Service\ActivityTypeDataProvider
};
use SharedContext\Domain\ValueObject\ {
    ActivityParticipantPriviledge,
    ActivityParticipantType
};
use Tests\TestBase;

class ActivityParticipantTest extends TestBase
{
    protected $activityType;
    protected $attendeeSetup;
    protected $priviledge;
    protected $id = "newId";
    protected $participantType = "coordinator", $canInitiate = false, $canAttend = true;
    protected $feedbackForm;
    protected $user;
    protected $meeting;
    protected $activityTypeDataProvider, $activityParticipantData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->activityType = $this->buildMockOfClass(ActivityType::class);
        $this->feedbackForm = $this->buildMockOfClass(FeedbackForm::class);
        
        $activityParticipantData = new ActivityParticipantData("consultant", true, true, null);
        $this->attendeeSetup = new TestableActivityParticipant($this->activityType, "id", $activityParticipantData);
        
        $this->priviledge = $this->buildMockOfClass(ActivityParticipantPriviledge::class);
        $this->attendeeSetup->participantPriviledge = $this->priviledge;
        
        $this->user = $this->buildMockOfInterface(CanAttendMeeting::class);
        $this->meeting = $this->buildMockOfClass(Meeting::class);
        
        $this->activityTypeDataProvider = $this->buildMockOfClass(ActivityTypeDataProvider::class);
    }
    
    protected function getActivityParticipantData()
    {
        return new ActivityParticipantData($this->participantType, $this->canInitiate, $this->canAttend, $this->feedbackForm);
    }
    
    protected function executeConstruct()
    {
        return new TestableActivityParticipant($this->activityType, $this->id, $this->getActivityParticipantData());
    }
    public function test_construct_setProperties()
    {
        $this->feedbackForm->expects($this->any())
                ->method("belongsToSameFirmAs")
                ->willReturn(true);
        
        $activityParticipant = $this->executeConstruct();
        $this->assertEquals($this->activityType, $activityParticipant->activityType);
        $this->assertEquals($this->id, $activityParticipant->id);
        
        $participantType = new ActivityParticipantType($this->participantType);
        $this->assertEquals($participantType, $activityParticipant->participantType);
        $participantPriviledge = new ActivityParticipantPriviledge($this->canInitiate, $this->canAttend);
        $this->assertEquals($participantPriviledge, $activityParticipant->participantPriviledge);
        
        $this->assertEquals($this->feedbackForm, $activityParticipant->reportForm);
        $this->assertFalse($activityParticipant->disabled);
    }
    public function test_construct_reportFormBelongsToDifferentFirm()
    {
        $this->feedbackForm->expects($this->once())
                ->method("belongsToSameFirmAs")
                ->with($this->activityType)
                ->willReturn(false);
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "forbidden: can only assignt feedback form in your firm";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_construct_emptyReport_constructNormally()
    {
        $this->feedbackForm = null;
        $this->executeConstruct();
        $this->markAsSuccess();
    }
    
    protected function executeUpdate()
    {
        $this->activityTypeDataProvider->expects($this->any())
                ->method("pullActivityParticipantDataCorrespondWithType")
                ->willReturn($this->getActivityParticipantData());
        $this->attendeeSetup->update($this->activityTypeDataProvider);
    }
    public function test_update_updatePriviledgeReportFormAndEnable()
    {
        $this->attendeeSetup->disabled = true;
        $this->executeUpdate();
        
        $participantPriviledge = new ActivityParticipantPriviledge($this->canInitiate, $this->canAttend);
        $this->assertEquals($participantPriviledge, $this->attendeeSetup->participantPriviledge);
        $this->assertEquals($this->feedbackForm, $this->attendeeSetup->reportForm);
        $this->assertFalse($this->attendeeSetup->disabled);
    }
    public function test_update_noResultFromDataProvider_disable()
    {
        $this->activityTypeDataProvider->expects($this->any())
                ->method("pullActivityParticipantDataCorrespondWithType")
                ->with($this->attendeeSetup->participantType->getParticipantType())
                ->willReturn(null);
        $this->executeUpdate();
        $this->assertTrue($this->attendeeSetup->disabled);
    }
    
    public function test_roleCorrespondWithUser_returnUsersRoleCorrespondWithParticipantTypeResult()
    {
        $this->user->expects($this->once())
                ->method("roleCorrespondWith")
                ->with($this->attendeeSetup->participantType);
        $this->attendeeSetup->roleCorrespondWithUser($this->user);
    }
    
    protected function executeSetUserAsInitiatorInMeeting()
    {
        $this->priviledge->expects($this->any())
                ->method("canInitiate")
                ->willReturn(true);
        $this->user->expects($this->any())
                ->method("roleCorrespondWith")
                ->willReturn(true);
        $this->attendeeSetup->setUserAsInitiatorInMeeting($this->meeting, $this->user);
    }
    public function test_setUserAsInitiatorInMeeting_setMeetingInitiator()
    {
        $this->meeting->expects($this->once())
                ->method("setInitiator")
                ->with($this->attendeeSetup, $this->user);
        $this->executeSetUserAsInitiatorInMeeting();
    }
    public function test_setUserAsInitiatorInMeeting_userNotCorrespondWithRole_noop()
    {
        $this->user->expects($this->once())
                ->method("roleCorrespondWith")
                ->with($this->attendeeSetup->participantType)
                ->willReturn(false);
        $this->meeting->expects($this->never())
                ->method("setInitiator");
        $this->executeSetUserAsInitiatorInMeeting();
    }
    public function test_setUserAsInitiatorInMeeting_noInitiatePriviledge_forbidden()
    {
        $this->priviledge->expects($this->once())
                ->method("canInitiate")
                ->willReturn(false);
        $operation = function (){
            $this->executeSetUserAsInitiatorInMeeting();
        };
        $errorDetail = "forbidden: user type cannot initiate this meeting";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeAddUserAsAttendeeInMeeting()
    {
        $this->priviledge->expects($this->any())
                ->method("canAttend")
                ->willReturn(true);
        $this->user->expects($this->any())
                ->method("roleCorrespondWith")
                ->willReturn(true);
        $this->attendeeSetup->addUserAsAttendeeInMeeting($this->meeting, $this->user);
    }
    public function test_addUserAsAttendeeInMeeting_setMeetingInitiator()
    {
        $this->meeting->expects($this->once())
                ->method("addAttendee")
                ->with($this->attendeeSetup, $this->user);
        $this->executeAddUserAsAttendeeInMeeting();
    }
    public function test_addUserAsAttendeeInMeeting_userNotCorrespondWithRole_noop()
    {
        $this->user->expects($this->once())
                ->method("roleCorrespondWith")
                ->with($this->attendeeSetup->participantType)
                ->willReturn(false);
        $this->meeting->expects($this->never())
                ->method("setInitiator");
        $this->executeAddUserAsAttendeeInMeeting();
    }
    public function test_addUserAsAttendeeInMeeting_noInitiatePriviledge_forbidden()
    {
        $this->priviledge->expects($this->once())
                ->method("canAttend")
                ->willReturn(false);
        $operation = function (){
            $this->executeAddUserAsAttendeeInMeeting();
        };
        $errorDetail = "forbidden: user type cannot attend this meeting";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
}

class TestableActivityParticipant extends ActivityParticipant
{
    public $activityType;
    public $id;
    public $participantType;
    public $participantPriviledge;
    public $reportForm;
    public $disabled;
}

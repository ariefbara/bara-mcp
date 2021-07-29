<?php

namespace Firm\Domain\Model\Firm\Program\ActivityType;

use Firm\Domain\Model\Firm\FeedbackForm;
use Firm\Domain\Model\Firm\Program\ActivityType;
use Firm\Domain\Model\Firm\Program\CanAttendMeeting;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Service\ActivityTypeDataProvider;
use SharedContext\Domain\ValueObject\ActivityParticipantPriviledge;
use SharedContext\Domain\ValueObject\ActivityParticipantType;
use Tests\TestBase;

class ActivityParticipantTest extends TestBase
{
    protected $activityType;
    protected $attendeeSetup;
    protected $activityParticipantType;
    protected $priviledge;
    protected $id = "newId";
    protected $participantType = "participant", $canInitiate = false, $canAttend = true;
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
        
        $this->activityParticipantType = $this->buildMockOfClass(ActivityParticipantType::class);
        $this->attendeeSetup->participantType = $this->activityParticipantType;
        
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
    
    protected function executeIsActiveTypeCorrespondWithRole()
    {
        $this->activityParticipantType->expects($this->any())
                ->method('sameValueAs')
                ->with($this->activityParticipantType)
                ->willReturn(true);
        return $this->attendeeSetup->isActiveTypeCorrespondWithRole($this->activityParticipantType);
    }
    public function test_isActiveTypeCorrespondWithRole_returnParticipantTypeSameValueResult()
    {
        $this->assertTrue($this->executeIsActiveTypeCorrespondWithRole());
    }
    public function test_isActiveTypeCorrespondWithRole_disabled_returnFalse()
    {
        $this->attendeeSetup->disabled = true;
        $this->assertFalse($this->executeIsActiveTypeCorrespondWithRole());
    }
    
    public function test_assertCanAttend_hasAttendPriviledge_void()
    {
        $this->priviledge->expects($this->any())
                ->method('canAttend')
                ->willReturn(true);
        $this->attendeeSetup->assertCanAttend();
        $this->markAsSuccess();
    }
    public function test_assertCanAttend_noAttendPriviledge_forbidden()
    {
        $this->priviledge->expects($this->once())
                ->method('canAttend')
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function (){
            $this->attendeeSetup->assertCanAttend();
        }, 'Forbidden', 'forbidden: insuficient role to cannot attend meeting');
    }
    
    public function test_assertCanInitiate_hasInitiatePriviledge_void()
    {
        $this->priviledge->expects($this->any())
                ->method('canInitiate')
                ->willReturn(true);
        $this->attendeeSetup->assertCanInitiate();
        $this->markAsSuccess();
    }
    public function test_assertCanInitiate_noInitiatePriviledge_forbidden()
    {
        $this->priviledge->expects($this->once())
                ->method('canInitiate')
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function (){
            $this->attendeeSetup->assertCanInitiate();
        }, 'Forbidden', 'forbidden: insuficient role to cannot initiate meeting');
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

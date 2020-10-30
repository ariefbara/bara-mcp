<?php

namespace Firm\Domain\Model\Firm\Program\ActivityType;

use Firm\Domain\Model\Firm\ {
    FeedbackForm,
    Program\ActivityType
};
use SharedContext\Domain\ValueObject\ {
    ActivityParticipantPriviledge,
    ActivityParticipantType
};
use Tests\TestBase;

class ActivityParticipantTest extends TestBase
{
    protected $activityType;
    protected $id = "newId";
    protected $participantType = "coordinator", $canInitiate = false, $canAttend = true;
    protected $feedbackForm;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->activityType = $this->buildMockOfClass(ActivityType::class);
        $this->feedbackForm = $this->buildMockOfClass(FeedbackForm::class);
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
}

class TestableActivityParticipant extends ActivityParticipant
{
    public $activityType;
    public $id;
    public $participantType;
    public $participantPriviledge;
    public $reportForm;
}

<?php

namespace Firm\Domain\Service;

use Firm\Domain\Model\Firm\ {
    FeedbackForm,
    Program\ActivityType\ActivityParticipantData
};
use Tests\TestBase;

class ActivityTypeDataProviderTest extends TestBase
{
    protected $feedbackFormRepository, $feedbackForm;
    protected $data;
    protected $name = "new name", $description = "new description";
    protected $participantType = "new participant type", $canInitiate = true, $canAttend = true, $feedbackFormId = "feedbackFormId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->feedbackForm = $this->buildMockOfClass(FeedbackForm::class);
        $this->feedbackFormRepository = $this->buildMockOfInterface(FeedbackFormRepository::class);
        $this->feedbackFormRepository->expects($this->any())
                ->method("aFeedbackFormOfId")
                ->with($this->feedbackFormId)
                ->willReturn($this->feedbackForm);
        
        $this->data = new TestableActivityTypeDataProvider($this->feedbackFormRepository, "name", "description");
    }
    
    public function test_construct_setProperties()
    {
        $data = new TestableActivityTypeDataProvider($this->feedbackFormRepository, $this->name, $this->description);
        $this->assertEquals($this->feedbackFormRepository, $data->feedbackFormRepository);
        $this->assertEquals($this->name, $data->name);
        $this->assertEquals($this->description, $data->description);
        $this->assertEquals([], $data->activityParticipantDataCollection);
    }
    
    protected function executeAddActivityParticipantData()
    {
        $this->data->addActivityParticipantData(
                $this->participantType, $this->canInitiate, $this->canAttend, $this->feedbackFormId);
    }
    public function test_addActivityParticipantData_addToCollection()
    {
        $this->executeAddActivityParticipantData();
        $activityParticipantData = new ActivityParticipantData(
                $this->participantType, $this->canInitiate, $this->canAttend, $this->feedbackForm);
        $this->assertEquals($activityParticipantData, $this->data->activityParticipantDataCollection[$this->participantType]);
    }
    public function test_addActivityParticipantData_emptyFeedbackFormId_setAsNull()
    {
        $this->feedbackFormId = null;
        $this->executeAddActivityParticipantData();
        $this->executeAddActivityParticipantData();
        $activityParticipantData = new ActivityParticipantData(
                $this->participantType, $this->canInitiate, $this->canAttend, null);
        $this->assertEquals($activityParticipantData, $this->data->activityParticipantDataCollection[$this->participantType]);
    }
}

class TestableActivityTypeDataProvider extends ActivityTypeDataProvider
{
    public $feedbackFormRepository;
    public $name;
    public $description;
    public $activityParticipantDataCollection;
}

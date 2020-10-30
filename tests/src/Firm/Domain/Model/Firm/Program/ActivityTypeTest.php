<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\ {
    Model\Firm,
    Model\Firm\Program,
    Model\Firm\Program\ActivityType\ActivityParticipant,
    Model\Firm\Program\ActivityType\ActivityParticipantData,
    Service\ActivityTypeDataProvider,
    Service\FeedbackFormRepository
};
use Tests\TestBase;

class ActivityTypeTest extends TestBase
{
    protected $program;
    protected $activityType;
    protected $id = "newId", $name = "new name", $description = "new description";
    protected $activityTypeDataProvider;
    protected $activityParticipantData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        
        $feedbackFormRepository = $this->buildMockOfInterface(FeedbackFormRepository::class);
        $activityTypeDataProvider = new ActivityTypeDataProvider($feedbackFormRepository, "name", "description");
        $this->activityType = new TestableActivityType($this->program, "id", $activityTypeDataProvider);
        
        $this->activityTypeDataProvider = $this->buildMockOfClass(ActivityTypeDataProvider::class);
        $this->activityParticipantData = $this->buildMockOfClass(ActivityParticipantData::class);
        $this->activityParticipantData->expects($this->any())->method("getParticipantType")->willReturn("consultant");
        
    }
    
    protected function executeConstruct()
    {
        $this->activityTypeDataProvider->expects($this->any())->method("getName")->willReturn($this->name);
        $this->activityTypeDataProvider->expects($this->any())->method("getDescription")->willReturn($this->description);
        return new TestableActivityType($this->program, $this->id, $this->activityTypeDataProvider);
    }
    public function test_construct_setProperties()
    {
        $activityType = $this->executeConstruct();
        $this->assertEquals($this->program, $activityType->program);
        $this->assertEquals($this->id, $activityType->id);
        $this->assertEquals($this->name, $activityType->name);
        $this->assertEquals($this->description, $activityType->description);
    }
    public function test_construct_emptyName_badRequest()
    {
        $this->name = "";
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: activity type name is mandatory";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_construct_addActivityParticipant()
    {
        $this->activityParticipantData->expects($this->any())->method("getParticipantType")->willReturn("coordinator");
        $this->activityParticipantData->expects($this->any())->method("getCanInitiate")->willReturn(false);
        $this->activityParticipantData->expects($this->any())->method("getCanAttend")->willReturn(true);
        $this->activityTypeDataProvider->expects($this->once())
                ->method("iterateActivityParticipantData")
                ->willReturn([$this->activityParticipantData]);
        $activityType = $this->executeConstruct();
        $this->assertEquals(1, $activityType->participants->count());
        $this->assertInstanceOf(ActivityParticipant::class, $activityType->participants->first());
    }
    
    public function test_belongsToFirm_returnProgramBelongsToFirmResult()
    {
        $firm = $this->buildMockOfClass(Firm::class);
        $this->program->expects($this->once())
                ->method("belongsToFirm")
                ->with($firm);
        $this->activityType->belongsToFirm($firm);
    }
}

class TestableActivityType extends ActivityType
{
    public $program;
    public $id;
    public $name;
    public $description;
    public $participants;
}

<?php

namespace ActivityCreator\Application\Service\Consultant;

use ActivityCreator\ {
    Application\Service\ActivityTypeRepository,
    Domain\DependencyModel\Firm\Personnel\Consultant,
    Domain\DependencyModel\Firm\Program\ActivityType,
    Domain\Model\ConsultantActivity
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class InitiateActivityTest extends TestBase
{
    protected $consultantActivityRepository, $nextId = "nextId";
    protected $consultantRepository, $consultant;
    protected $activityTypeRepository, $activityType;
    protected $dispatcher;
    protected $service;
    protected $firmId = "firmId", $personnelId = "personnelId", $consultantId = "consultantId", 
            $activityTypeId = "activityTypeId";
    protected $activityDataProvider = "string represent data provider";

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultantActivityRepository = $this->buildMockOfInterface(ConsultantActivityRepository::class);
        $this->consultantActivityRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextId);

        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->consultantRepository = $this->buildMockOfInterface(ConsultantRepository::class);
        $this->consultantRepository->expects($this->any())
                ->method("aConsultantBelongsToPersonnel")
                ->with($this->firmId, $this->personnelId, $this->consultantId)
                ->willReturn($this->consultant);

        $this->activityType = $this->buildMockOfClass(ActivityType::class);
        $this->activityTypeRepository = $this->buildMockOfInterface(ActivityTypeRepository::class);
        $this->activityTypeRepository->expects($this->any())
                ->method("ofId")
                ->with($this->activityTypeId)
                ->willReturn($this->activityType);

        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new InitiateActivity(
                $this->consultantActivityRepository, $this->consultantRepository, $this->activityTypeRepository,
                $this->dispatcher);
    }

    protected function execute()
    {
        return $this->service->execute(
                        $this->firmId, $this->personnelId, $this->consultantId, $this->activityTypeId,
                        $this->activityDataProvider);
    }
    public function test_execute_addActivityToRepository()
    {
        $this->consultant->expects($this->once())
                ->method("initiateActivity")
                ->with($this->nextId, $this->activityType, $this->activityDataProvider);
        $this->consultantActivityRepository->expects($this->once())
                ->method("add");
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }
    public function test_execute_dispatcheActivity()
    {
        $consultantActivity = $this->buildMockOfClass(ConsultantActivity::class);
        $this->consultant->expects($this->once())
                ->method("initiateActivity")
                ->willReturn($consultantActivity);
        $this->dispatcher->expects($this->once())
                ->method("dispatch")
                ->with($consultantActivity);
        $this->execute();
    }
}

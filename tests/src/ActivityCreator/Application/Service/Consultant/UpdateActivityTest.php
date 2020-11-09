<?php

namespace ActivityCreator\Application\Service\Consultant;

use ActivityCreator\Domain\ {
    Model\ConsultantActivity,
    service\ActivityDataProvider
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class UpdateActivityTest extends TestBase
{
    protected $consultantActivityRepository, $consultantActivity;
    protected $dispatcher;
    protected $service;
    protected $firmId = "firmId", $personnelId = "personnelId", $consultantActivityId = "consultantActivityId";
    protected $activityDataProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultantActivity = $this->buildMockOfClass(ConsultantActivity::class);
        $this->consultantActivityRepository = $this->buildMockOfInterface(ConsultantActivityRepository::class);
        $this->consultantActivityRepository->expects($this->any())
                ->method('aConsultantActivityBelongsToPersonnel')
                ->with($this->firmId, $this->personnelId, $this->consultantActivityId)
                ->willReturn($this->consultantActivity);

        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new UpdateActivity($this->consultantActivityRepository, $this->dispatcher);
        $this->activityDataProvider = $this->buildMockOfClass(ActivityDataProvider::class);
    }

    protected function execute()
    {
        $this->service->execute(
                $this->firmId, $this->personnelId, $this->consultantActivityId, $this->activityDataProvider);
    }
    public function test_execute_updateConsultantActivity()
    {
        $this->consultantActivity->expects($this->once())
                ->method("update")
                ->with($this->activityDataProvider);
        $this->execute();
    }
    public function test_updateRepository()
    {
        $this->consultantActivityRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }

    public function test_execute_dispathceConsultantActivity()
    {
        $this->dispatcher->expects($this->once())
                ->method("dispatch");
        $this->execute();
    }
}

<?php

namespace ActivityCreator\Application\Service\ClientParticipant;

use ActivityCreator\Domain\ {
    Model\ParticipantActivity,
    service\ActivityDataProvider
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class UpdateActivityTest extends TestBase
{
    protected $participantActivityRepository, $participantActivity;
    protected $dispatcher;
    protected $service;
    protected $firmId = "firmId", $clientId = "clientId", $clientParticipantActivityId = "clientParticipantActivityId";
    protected $activityDataProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participantActivity = $this->buildMockOfClass(ParticipantActivity::class);
        $this->participantActivityRepository = $this->buildMockOfInterface(ParticipantActivityRepository::class);
        $this->participantActivityRepository->expects($this->any())
                ->method('aParticipantActivityBelongsToClient')
                ->with($this->firmId, $this->clientId, $this->clientParticipantActivityId)
                ->willReturn($this->participantActivity);

        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new UpdateActivity($this->participantActivityRepository, $this->dispatcher);
        $this->activityDataProvider = $this->buildMockOfClass(ActivityDataProvider::class);
    }

    protected function execute()
    {
        $this->service->execute(
                $this->firmId, $this->clientId, $this->clientParticipantActivityId, $this->activityDataProvider);
    }
    public function test_execute_updateParticipantActivity()
    {
        $this->participantActivity->expects($this->once())
                ->method("update")
                ->with($this->activityDataProvider);
        $this->execute();
    }
    public function test_updateRepository()
    {
        $this->participantActivityRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }

    public function test_execute_dispathceParticipantActivity()
    {
        $this->dispatcher->expects($this->once())
                ->method("dispatch");
        $this->execute();
    }
}

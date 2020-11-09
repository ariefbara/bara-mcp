<?php

namespace ActivityCreator\Application\Service\UserParticipant;

use ActivityCreator\ {
    Application\Service\ActivityTypeRepository,
    Domain\DependencyModel\Firm\Program\ActivityType,
    Domain\DependencyModel\User\ProgramParticipation,
    Domain\Model\ParticipantActivity
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;


class InitiateActivityTest extends TestBase
{

    protected $participantActivityRepository, $nextId = "nextId";
    protected $userParticipantRepository, $programParticipation;
    protected $activityTypeRepository, $activityType;
    protected $dispatcher;
    protected $service;
    protected $userId = "userId", $programParticipationId = "programParticipationId", $activityTypeId = "activityTypeId";
    protected $activityDataProvider = "string represent data provider";

    protected function setUp(): void
    {
        parent::setUp();
        $this->participantActivityRepository = $this->buildMockOfInterface(ParticipantActivityRepository::class);
        $this->participantActivityRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextId);

        $this->programParticipation = $this->buildMockOfClass(ProgramParticipation::class);
        $this->userParticipantRepository = $this->buildMockOfInterface(UserParticipantRepository::class);
        $this->userParticipantRepository->expects($this->any())
                ->method("aProgramParticipationBelongsToUser")
                ->with($this->userId, $this->programParticipationId)
                ->willReturn($this->programParticipation);

        $this->activityType = $this->buildMockOfClass(ActivityType::class);
        $this->activityTypeRepository = $this->buildMockOfInterface(ActivityTypeRepository::class);
        $this->activityTypeRepository->expects($this->any())
                ->method("ofId")
                ->with($this->activityTypeId)
                ->willReturn($this->activityType);

        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new InitiateActivity(
                $this->participantActivityRepository, $this->userParticipantRepository, $this->activityTypeRepository,
                $this->dispatcher);
    }

    protected function execute()
    {
        return $this->service->execute(
                        $this->userId, $this->programParticipationId, $this->activityTypeId, $this->activityDataProvider);
    }
    public function test_execute_addActivityToRepository()
    {
        $this->programParticipation->expects($this->once())
                ->method("initiateActivity")
                ->with($this->nextId, $this->activityType, $this->activityDataProvider);
        $this->participantActivityRepository->expects($this->once())
                ->method("add");
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }
    public function test_execute_dispatcheActivity()
    {
        $participantActivity = $this->buildMockOfClass(ParticipantActivity::class);
        $this->programParticipation->expects($this->once())
                ->method("initiateActivity")
                ->willReturn($participantActivity);
        $this->dispatcher->expects($this->once())
                ->method("dispatch")
                ->with($participantActivity);
        $this->execute();
    }

}

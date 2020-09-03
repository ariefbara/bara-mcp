<?php

namespace Participant\Application\Service\UserParticipant;

use DateTimeImmutable;
use Participant\ {
    Application\Service\UserParticipantRepository,
    Domain\Model\UserParticipant
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class UserParticipantChangeConsultationRequestTimeTest extends TestBase
{
    protected $service;
    protected $userParticipantRepository, $userParticipant;
    protected $dispatcher;
    protected $userId = 'userId', $userParticipantId = 'userParticipantId', $consultationRequestId = 'negotiate-schedule-id';
    protected $startTime;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userParticipant = $this->buildMockOfClass(UserParticipant::class);
        $this->userParticipantRepository = $this->buildMockOfInterface(UserParticipantRepository::class);
        $this->userParticipantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->userId, $this->userParticipantId)
                ->willReturn($this->userParticipant);
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new UserParticipantChangeConsultationRequestTime($this->userParticipantRepository, $this->dispatcher);

        $this->startTime = new DateTimeImmutable();
    }

    protected function execute()
    {
        $this->service->execute($this->userId, $this->userParticipantId, $this->consultationRequestId,
                $this->startTime);
    }

    public function test_execute_reProposeNegotiateMentoringScheduleInUserParticipant()
    {
        $this->userParticipant->expects($this->once())
                ->method('reproposeConsultationRequest')
                ->with($this->consultationRequestId, $this->startTime);
        $this->execute();
    }

    public function test_execute_updateUserParticipantRepository()
    {
        $this->userParticipantRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }

    public function test_execute_dispatchDispatcher()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->userParticipant);
        $this->execute();
    }
}

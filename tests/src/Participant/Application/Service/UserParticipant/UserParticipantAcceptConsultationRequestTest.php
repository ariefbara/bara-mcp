<?php

namespace Participant\Application\Service\UserParticipant;

use DateTimeImmutable;
use Participant\ {
    Application\Service\UserParticipantRepository,
    Domain\Model\UserParticipant
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class UserParticipantAcceptConsultationRequestTest extends TestBase
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
        
        $this->service = new UserParticipantAcceptConsultationRequest($this->userParticipantRepository, $this->dispatcher);
        
        $this->startTime = new DateTimeImmutable();
    }
    protected function execute()
    {
        $this->service->execute($this->userId, $this->userParticipantId, $this->consultationRequestId);
    }
    public function test_execute_executeUserParticipantsAcceptConsultationRequestMethod()
    {
        $this->userParticipant->expects($this->once())
            ->method('acceptConsultationRequest')
            ->with($this->consultationRequestId);
        $this->execute();
    }
    public function test_execute_updateRepository()
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

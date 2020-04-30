<?php

namespace Client\Application\Service\Client\ProgramParticipation\ConsultationSession;

use Client\{
    Application\Service\Client\ProgramParticipation\ConsultationSessionRepository,
    Domain\Model\Client\ProgramParticipation\ConsultationSession,
    Domain\Model\Client\ProgramParticipation\ConsultationSession\ConsultationSessionNotification
};
use Tests\TestBase;

class ConsultationSessionNotificationAddTest extends TestBase
{

    protected $service;
    protected $consultationSessionNotificationRepository;
    protected $firmId = 'firmId', $personnelId = 'personnelId', $consultantId = 'consultantId';
    protected $consultationSessionRepository, $consultationSession, $consultationSessionId = 'consultationSessionId';
    protected $id = 'id', $message = 'message';

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSessionNotificationRepository = $this->buildMockOfInterface(ConsultationSessionNotificationRepository::class);
        $this->consultationSessionNotificationRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->id);

        $this->consultationSessionRepository = $this->buildMockOfInterface(ConsultationSessionRepository::class);
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->consultationSessionRepository->expects($this->once())
                ->method('aConsultationSessionOfConsultant')
                ->with($this->firmId, $this->personnelId, $this->consultantId, $this->consultationSessionId)
                ->willReturn($this->consultationSession);

        $this->service = new ConsultationSessionNotificationAdd(
                $this->consultationSessionNotificationRepository, $this->consultationSessionRepository);
    }

    public function test_execute_addConsultationSessionNotificationToRepository()
    {
        $consultationSessionNotification = $this->buildMockOfClass(ConsultationSessionNotification::class);
        $this->consultationSession->expects($this->once())
                ->method('createConsultationSessionNotification')
                ->with($this->id, $this->message)
                ->willReturn($consultationSessionNotification);
        $this->consultationSessionNotificationRepository->expects($this->once())
                ->method('add')
                ->with($consultationSessionNotification);
        $this->service->execute(
                $this->firmId, $this->personnelId, $this->consultantId, $this->consultationSessionId, $this->message);
    }

}

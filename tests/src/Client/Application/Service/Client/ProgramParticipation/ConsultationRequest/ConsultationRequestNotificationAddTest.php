<?php

namespace Client\Application\Service\Client\ProgramParticipation\ConsultationRequest;

use Client\ {
    Application\Service\Client\ProgramParticipation\ConsultationRequestRepository,
    Domain\Model\Client\ProgramParticipation\ConsultationRequest,
    Domain\Model\Client\ProgramParticipation\ConsultationRequest\ConsultationRequestNotification
};
use Tests\TestBase;

class ConsultationRequestNotificationAddTest extends TestBase
{
    protected $service;
    protected $consultationRequestNotificationRepository;
    protected $firmId = 'firmId', $personnelId = 'personnelId', $consultantId = 'consultantId';
    protected $consultationRequestRepository, $consultationRequest, $consultationRequestId = 'consultationRequestId';
    protected $id = 'id', $message = 'message';

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequestNotificationRepository = $this->buildMockOfInterface(ConsultationRequestNotificationRepository::class);
        $this->consultationRequestNotificationRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->id);

        $this->consultationRequestRepository = $this->buildMockOfInterface(ConsultationRequestRepository::class);
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->consultationRequestRepository->expects($this->once())
                ->method('aConsultationRequestOfConsultant')
                ->with($this->firmId, $this->personnelId, $this->consultantId, $this->consultationRequestId)
                ->willReturn($this->consultationRequest);

        $this->service = new ConsultationRequestNotificationAdd(
                $this->consultationRequestNotificationRepository, $this->consultationRequestRepository);
    }
    
    public function test_execute_addConsultationRequestNotificationToRepository()
    {
        $consultationRequestNotification = $this->buildMockOfClass(ConsultationRequestNotification::class);
        $this->consultationRequest->expects($this->once())
                ->method('createConsultationRequestNotification')
                ->with($this->id, $this->message)
                ->willReturn($consultationRequestNotification);
        $this->consultationRequestNotificationRepository->expects($this->once())
                ->method('add')
                ->with($consultationRequestNotification);
        $this->service->execute(
                $this->firmId, $this->personnelId, $this->consultantId, $this->consultationRequestId, $this->message);
    }
}

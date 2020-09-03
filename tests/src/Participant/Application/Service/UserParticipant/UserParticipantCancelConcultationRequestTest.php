<?php

namespace Participant\Application\Service\UserParticipant;

use Participant\ {
    Application\Service\Participant\ConsultationRequestRepository,
    Domain\Model\Participant\ConsultationRequest
};
use Tests\TestBase;

class UserParticipantCancelConcultationRequestTest extends TestBase
{
    protected $service;
    protected $consultationRequestRepository, $consultationRequest;
    
    protected $userId = 'userId', $userParticipantId = 'userParticipantId', $consultationRequestId = 'consultationRequestid';

    protected function setUp(): void
    {
        parent::setUp();

        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->consultationRequestRepository = $this->buildMockOfInterface(ConsultationRequestRepository::class);
        $this->consultationRequestRepository->expects($this->any())
            ->method('aConsultationRequestFromUserParticipant')
            ->with($this->userId, $this->userParticipantId, $this->consultationRequestId)
            ->willReturn($this->consultationRequest);

        $this->service = new UserParticipantCancelConcultationRequest($this->consultationRequestRepository);

    }

    protected function execute()
    {
        $this->service->execute($this->userId, $this->userParticipantId, $this->consultationRequestId);
    }

    public function test_cancel_cancelConsultationRequest()
    {
        $this->consultationRequest->expects($this->once())
            ->method('cancel');
        $this->execute();
    }

    public function test_cancel_updateRepositorys()
    {
        $this->consultationRequestRepository->expects($this->once())
            ->method('update');
        $this->execute();
    }
}

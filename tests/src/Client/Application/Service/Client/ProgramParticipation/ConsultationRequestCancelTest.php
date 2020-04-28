<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\Domain\Model\Client\ProgramParticipation\ConsultationRequest;
use Tests\TestBase;

class ConsultationRequestCancelTest extends TestBase
{

    protected $consultationRequestRepository, $consultationRequest, $programParticipationCompositionId,
        $consultationRequestId = 'negotiate-schedule-id';
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->programParticipationCompositionId = $this->buildMockOfClass(ProgramParticipationCompositionId::class);
        $this->consultationRequestRepository = $this->buildMockOfInterface(ConsultationRequestRepository::class);
        $this->consultationRequestRepository->expects($this->any())
            ->method('ofId')
            ->with($this->programParticipationCompositionId, $this->consultationRequestId)
            ->willReturn($this->consultationRequest);

        $this->service = new ConsultationRequestCancel($this->consultationRequestRepository);

    }

    protected function execute()
    {
        $this->service->execute($this->programParticipationCompositionId, $this->consultationRequestId);
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

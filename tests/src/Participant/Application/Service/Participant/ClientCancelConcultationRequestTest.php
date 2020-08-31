<?php

namespace Participant\Application\Service\Participant;

use Participant\Domain\Model\Participant\ConsultationRequest;
use Tests\TestBase;

class ClientCancelConcultationRequestTest extends TestBase
{

    protected $service;
    protected $consultationRequestRepository, $consultationRequest;
    
    protected $firmId = 'firmId', $clientId = 'clientId', $programId = 'programId', $consultationRequestId = 'consultationRequestid';

    protected function setUp(): void
    {
        parent::setUp();

        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->consultationRequestRepository = $this->buildMockOfInterface(ConsultationRequestRepository::class);
        $this->consultationRequestRepository->expects($this->any())
            ->method('consultationRequestFromClient')
            ->with($this->firmId, $this->clientId, $this->programId, $this->consultationRequestId)
            ->willReturn($this->consultationRequest);

        $this->service = new ClientCancelConcultationRequest($this->consultationRequestRepository);

    }

    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->programId, $this->consultationRequestId);
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

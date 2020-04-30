<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest;
use Tests\TestBase;

class ConsultationRequestRejectTest extends TestBase
{
    protected $service;
    protected $programConsultantCompositionId;
    protected $consultationRequestRepository, $consultationRequest, $consultationRequestId = 'negotiate-mentoring-schedule-id';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programConsultantCompositionId = $this->buildMockOfClass(ProgramConsultantCompositionId::class);
        
        $this->consultationRequestRepository = $this->buildMockOfInterface(ConsultationRequestRepository::class);
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->consultationRequestRepository->expects($this->any())
            ->method('ofId')
            ->with($this->programConsultantCompositionId, $this->consultationRequestId)
            ->willReturn($this->consultationRequest);
        
        $this->service = new ConsultationRequestReject($this->consultationRequestRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->programConsultantCompositionId, $this->consultationRequestId);
    }
    public function test_execute_rejectConsultationRequest()
    {
        $this->consultationRequest->expects($this->once())
            ->method('reject');
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->consultationRequestRepository->expects($this->once())
            ->method('update');
        $this->execute();
    }
}

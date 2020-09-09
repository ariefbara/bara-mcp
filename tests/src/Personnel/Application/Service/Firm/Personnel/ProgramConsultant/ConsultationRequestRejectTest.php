<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest;
use Tests\TestBase;

class ConsultationRequestRejectTest extends TestBase
{
    protected $service;
    protected $consultationRequestRepository, $consultationRequest;
    
    protected $firmId = 'firmId', $personnelId = 'personnelId', $programConsultationId = 'programConsultationId', 
            $consultationRequestId = 'consultationRequetId';


    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->consultationRequestRepository = $this->buildMockOfInterface(ConsultationRequestRepository::class);
        $this->consultationRequestRepository->expects($this->any())
            ->method('ofId')
            ->with($this->firmId, $this->personnelId, $this->programConsultationId, $this->consultationRequestId)
            ->willReturn($this->consultationRequest);
        
        $this->service = new ConsultationRequestReject($this->consultationRequestRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->programConsultationId, $this->consultationRequestId);
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

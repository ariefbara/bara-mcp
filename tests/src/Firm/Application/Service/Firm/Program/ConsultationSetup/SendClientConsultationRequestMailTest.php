<?php

namespace Firm\Application\Service\Firm\Program\ConsultationSetup;

use Firm\Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest;
use Resources\Application\Service\Mailer;
use Tests\TestBase;

class SendClientConsultationRequestMailTest extends TestBase
{
    protected $service;
    protected $consultationRequestRepository, $consultationRequest;
    protected $mailer;
    
    protected $firmId = 'firmId', $clientId = 'clientId', $programId = 'programId', $consultationRequestId = 'consultationRequestId';
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->consultationRequestRepository = $this->buildMockOfInterface(ConsultationRequestRepository::class);
        $this->consultationRequestRepository->expects($this->any())
                ->method('aConsultationRequestOfClient')
                ->with($this->firmId, $this->clientId, $this->programId, $this->consultationRequestId)
                ->willReturn($this->consultationRequest);
        
        $this->mailer = $this->buildMockOfInterface(Mailer::class);
        
        $this->service = new SendClientConsultationRequestMail($this->consultationRequestRepository, $this->mailer);
    }
    
    public function test_execute_sendConsultationRequestMail()
    {
        $this->consultationRequest->expects($this->once())
                ->method('sendMail')
                ->with($this->mailer);
        $this->service->execute($this->firmId, $this->clientId, $this->programId, $this->consultationRequestId);
    }
}

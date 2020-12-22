<?php

namespace Notification\Application\Service;

use Notification\Domain\Model\Firm\Program\Participant\ConsultationRequest;
use SharedContext\Domain\ValueObject\MailMessageBuilder;
use Tests\TestBase;

class GenerateNotificationWhenConsultationRequestRejectedTest extends TestBase
{
    protected $consultationRequestRepository, $consultationRequest;
    protected $service;
    protected $consultationRequestId = "consultatioNRequestId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->consultationRequestRepository = $this->buildMockOfInterface(ConsultationRequestRepository::class);
        $this->consultationRequestRepository->expects($this->any())
                ->method("ofId")
                ->with($this->consultationRequestId)
                ->willReturn($this->consultationRequest);
        
        $this->service = new GenerateNotificationWhenConsultationRequestRejected($this->consultationRequestRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->consultationRequestId);
    }
    
    public function test_executeConsultationRequest_scenario_expectedResult()
    {
        $this->consultationRequest->expects($this->once())
                ->method("createNotificationTriggeredByConsultant")
                ->with(MailMessageBuilder::CONSULTATION_REJECTED);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->consultationRequestRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}

<?php

namespace Notification\Application\Service;

use Notification\Domain\Model\Firm\Program\Participant\ConsultationSession;
use Tests\TestBase;

class GenerateNotificationWhenConsultationSessionAcceptedByConsultantTest extends TestBase
{
    protected $consultationSessionRepository, $consultationSession;
    protected $service;
    protected $consultationSessionId = "consultationSessionId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->consultationSessionRepository = $this->buildMockOfInterface(ConsultationSessionRepository::class);
        $this->consultationSessionRepository->expects($this->any())
                ->method("ofId")
                ->with($this->consultationSessionId)
                ->willReturn($this->consultationSession);
        
        $this->service = new GenerateNotificationWhenConsultationSessionAcceptedByConsultant($this->consultationSessionRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->consultationSessionId);
    }
    public function test_execute_executeConsultationSessionsAddAcceptNotificationTriggeredByConsultantMethod()
    {
        $this->consultationSession->expects($this->once())
                ->method("addAcceptNotificationTriggeredByConsultant");
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->consultationSessionRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}

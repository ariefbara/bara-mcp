<?php

namespace Notification\Application\Service;

use Notification\Domain\Model\Firm\Program\Participant\ConsultationSession;
use Tests\TestBase;

class GenerateNotificationWhenConsultationSessionScheduledByParticipantTest extends TestBase
{
    protected $consultationSessionRepository, $consultationSession;
    protected $service;
    protected $consultationSessionId = "consultationSessionId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->consultationSessionRepository = $this->buildMockOfInterface(ConsultationSessionRepository::class);
        $this->consultationSessionRepository->expects($this->once())
                ->method("ofId")
                ->with($this->consultationSessionId)
                ->willReturn($this->consultationSession);
        
        $this->service = new GenerateNotificationWhenConsultationSessionScheduledByParticipant($this->consultationSessionRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->consultationSessionId);
    }
    public function test_execute_addConsultationSessionAcceptNotificationTriggeredByParticipant()
    {
        $this->consultationSession->expects($this->once())
                ->method("addAcceptNotificationTriggeredByParticipant");
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->consultationSessionRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}

<?php

namespace Firm\Application\Service\Firm\Program\ConsultationSetup;

use Firm\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession;
use Resources\Application\Service\Mailer;
use Tests\TestBase;

class SendClientConsultationSessionMailTest extends TestBase
{
    protected $service;
    protected $consultationSessionRepository, $consultationSession;
    protected $mailer;
    
    protected $firmId = 'firmId', $clientId = 'clientId', $programId = 'programId', $consultationSessionId = 'consultationSessionId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->consultationSessionRepository = $this->buildMockOfInterface(ConsultationSessionRepository::class);
        $this->consultationSessionRepository->expects($this->any())
                ->method('aConsultationSessionOfClient')
                ->with($this->firmId, $this->clientId, $this->programId, $this->consultationSessionId)
                ->willReturn($this->consultationSession);
        
        $this->mailer = $this->buildMockOfInterface(Mailer::class);
        
        $this->service = new SendClientConsultationSessionMail($this->consultationSessionRepository, $this->mailer);
    }
    
    public function test_execute_sendConsultationSessionMail()
    {
        $this->consultationSession->expects($this->once())
                ->method('sendMail')
                ->with($this->mailer);
        $this->service->execute($this->firmId, $this->clientId, $this->programId, $this->consultationSessionId);
    }
}

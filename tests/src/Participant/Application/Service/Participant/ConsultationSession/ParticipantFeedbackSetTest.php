<?php

namespace Participant\Application\Service\Participant\ConsultationSession;

use Participant\ {
    Application\Service\Participant\ConsultationSessionRepository,
    Domain\Model\Participant\ConsultationSession
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class ParticipantFeedbackSetTest extends TestBase
{

    protected $service;
    protected $consultationSessionRepository, $consultationSession;
    protected $firmId = 'firmId', $clientId = 'clientId', $programId = 'programId', $consultationSessionId = 'consultationSessionId';
    protected $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->consultationSessionRepository = $this->buildMockOfInterface(ConsultationSessionRepository::class);
        $this->consultationSessionRepository->expects($this->any())
                ->method('aConsultationSessionOfClientParticipant')
                ->with($this->firmId, $this->clientId, $this->programId, $this->consultationSessionId)
                ->willReturn($this->consultationSession);

        $this->service = new ParticipantFeedbackSet($this->consultationSessionRepository);

        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }

    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->programId, $this->consultationSessionId, $this->formRecordData);
    }
    
    public function test_execute_setParticipantFeedbackInConsultationSession()
    {
        $this->consultationSession->expects($this->once())
                ->method('setParticipantFeedback')
                ->with($this->formRecordData);
        $this->execute();
    }
    public function test_execute_updateConsultationSessionRepository()
    {
        $this->consultationSessionRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }

}

<?php

namespace Client\Application\Service\Client\ProgramParticipation\ConsultationSession;

use Client\{
    Application\Service\Client\ProgramParticipation\ConsultationSessionRepository,
    Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId,
    Domain\Model\Client\ProgramParticipation\ConsultationSession
};
use Shared\Domain\Model\FormRecordData;
use Tests\TestBase;

class ParticipantFeedbackSetTest extends TestBase
{

    protected $service;
    protected $programParticipationCompositionId;
    protected $consultationSessionRepository, $consultationSession, $consultationSessionId = 'consultationSessionId';
    protected $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->programParticipationCompositionId = $this->buildMockOfClass(ProgramParticipationCompositionId::class);
        $this->consultationSessionRepository = $this->buildMockOfInterface(ConsultationSessionRepository::class);
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->consultationSessionRepository->expects($this->any())
                ->method('ofId')
                ->with($this->programParticipationCompositionId, $this->consultationSessionId)
                ->willReturn($this->consultationSession);

        $this->service = new ParticipantFeedbackSet($this->consultationSessionRepository);

        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }

    protected function execute()
    {
        $this->service->execute(
                $this->programParticipationCompositionId, $this->consultationSessionId, $this->formRecordData);
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

<?php

namespace Participant\Application\Service\UserParticipant\ConsultationSession;

use Participant\ {
    Application\Service\Participant\ConsultationSessionRepository,
    Domain\Model\Participant\ConsultationSession
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class SubmitFeedbackTest extends TestBase
{
    protected $service;
    protected $consultationSessionRepository, $consultationSession;
    protected $userId = 'userId', $userParticipantId = 'userParticipantId', $consultationSessionId = 'consultationSessionId';
    protected $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->consultationSessionRepository = $this->buildMockOfInterface(ConsultationSessionRepository::class);
        $this->consultationSessionRepository->expects($this->any())
                ->method('aConsultationSessionOfUserParticipant')
                ->with($this->userId, $this->userParticipantId, $this->consultationSessionId)
                ->willReturn($this->consultationSession);

        $this->service = new SubmitFeedback($this->consultationSessionRepository);

        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }

    protected function execute()
    {
        $this->service->execute($this->userId, $this->userParticipantId, $this->consultationSessionId, $this->formRecordData);
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

<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ConsultationSession;

use Personnel\ {
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationSessionRepository,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class ConsultantFeedbackSetTest extends TestBase
{

    protected $service;
    protected $programConsultationCompositionId;
    protected $consultationSessionRepository, $consultationSession;
    protected $firmId = 'firmId', $personnelId = 'personnelId', $programConsultationId = 'programConsultationId',
            $consultationSessionId = 'consultationSessionId';
    protected $formRecordData, $participantRating = 4;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->consultationSessionRepository = $this->buildMockOfInterface(ConsultationSessionRepository::class);
        $this->consultationSessionRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->personnelId, $this->programConsultationId, $this->consultationSessionId)
                ->willReturn($this->consultationSession);

        $this->service = new ConsultantFeedbackSet($this->consultationSessionRepository);

        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }

    protected function execute()
    {
        $this->service->execute(
                $this->firmId, $this->personnelId, $this->programConsultationId, $this->consultationSessionId,
                $this->formRecordData, $this->participantRating);
    }

    public function test_execute_setConsultationSessionConsultantFeedback()
    {
        $this->consultationSession->expects($this->once())
                ->method('setConsultantFeedback')
                ->with($this->formRecordData, $this->participantRating);
        $this->execute();
    }

    public function test_execute_updateRepository()
    {
        $this->consultationSessionRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }

}

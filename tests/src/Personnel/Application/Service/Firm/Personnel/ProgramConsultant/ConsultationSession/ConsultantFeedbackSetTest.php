<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ConsultationSession;

use Personnel\ {
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationSessionRepository,
    Application\Service\Firm\Personnel\ProgramConsultant\ProgramConsultantCompositionId,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession
};
use Shared\Domain\Model\FormRecordData;
use Tests\TestBase;

class ConsultantFeedbackSetTest extends TestBase
{
    protected $service;
    protected $programConsultationCompositionId;
    protected $consultationSessionRepository, $consultationSession, $consultationSessionId = 'consultationSessionId';
    
    protected $formRecordData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programConsultationCompositionId = $this->buildMockOfClass(ProgramConsultantCompositionId::class);
        $this->consultationSessionRepository = $this->buildMockOfInterface(ConsultationSessionRepository::class);
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->consultationSessionRepository->expects($this->any())
                ->method('ofId')
                ->with($this->programConsultationCompositionId, $this->consultationSessionId)
                ->willReturn($this->consultationSession);
        $this->service = new ConsultantFeedbackSet($this->consultationSessionRepository);
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->programConsultationCompositionId, $this->consultationSessionId, $this->formRecordData);
    }
    public function test_execute_setConsultationSessionConsultantFeedback()
    {
        $this->consultationSession->expects($this->once())
                ->method('setConsultantFeedback')
                ->with($this->formRecordData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->consultationSessionRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}

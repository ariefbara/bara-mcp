<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\Firm\ConsultationFeedbackForm;
use Tests\TestBase;

class ConsultationFeedbackFormRemoveTest extends TestBase
{
    protected $service;
    protected $consultationFeedbackFormRepository, $consultationFeedbackForm, $firmId = 'firm-id',
        $consultationFeedbackFormId = 'consultation-feedback-form-id';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationFeedbackForm = $this->buildMockOfClass(ConsultationFeedbackForm::class);
        $this->consultationFeedbackFormRepository = $this->buildMockOfInterface(ConsultationFeedbackFormRepository::class);
        $this->consultationFeedbackFormRepository->expects($this->any())
            ->method('ofId')
            ->with($this->firmId, $this->consultationFeedbackFormId)
            ->willReturn($this->consultationFeedbackForm);
        
        $this->service = new ConsultationFeedbackFormRemove($this->consultationFeedbackFormRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->consultationFeedbackFormId);
    }
    
    public function test_remove_removeConsultationFeedbackForm()
    {
        $this->consultationFeedbackForm->expects($this->once())
            ->method('remove');
        $this->execute();
    }
    public function test_remove_updateRepository()
    {
        $this->consultationFeedbackFormRepository->expects($this->once())
            ->method('update');
        $this->execute();
    }
    
}

<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\ {
    Firm\ConsultationFeedbackForm,
    Shared\FormData
};
use Tests\TestBase;

class ConsultationFeedbackFormUpdateTest extends TestBase
{

    protected $consultationFeedbackFormRepository, $consultationFeedbackForm, $firmId = 'firm-id',
        $consultationFeedbackFormId = 'consultation-feedback-form-id';
    
    protected $service;
    protected $formData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationFeedbackForm = $this->buildMockOfClass(ConsultationFeedbackForm::class);
        $this->consultationFeedbackFormRepository = $this->buildMockOfInterface(ConsultationFeedbackFormRepository::class);
        $this->consultationFeedbackFormRepository->expects($this->any())
            ->method('ofId')
            ->with($this->firmId, $this->consultationFeedbackFormId)
            ->willReturn($this->consultationFeedbackForm);

        $this->service = new ConsultationFeedbackFormUpdate($this->consultationFeedbackFormRepository);
        $this->formData = $this->buildMockOfClass(FormData::class);
        $this->formData->expects($this->any())
                ->method('getName')
                ->willReturn('new form name');
    }

    protected function execute()
    {
        $this->service->execute($this->firmId, $this->consultationFeedbackFormId, $this->formData);
    }
    public function test_execute_updateConsultationFeedbackForm()
    {
        $this->consultationFeedbackForm->expects($this->once())
            ->method('update')
            ->with($this->formData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->consultationFeedbackFormRepository->expects($this->once())
            ->method('update');
        $this->execute();
    }
    
}

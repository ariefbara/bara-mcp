<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\ {
    Firm\FeedbackForm,
    Shared\FormData
};
use Tests\TestBase;

class FeedbackFormUpdateTest extends TestBase
{

    protected $feedbackFormRepository, $feedbackForm, $firmId = 'firm-id',
        $feedbackFormId = 'consultation-feedback-form-id';
    
    protected $service;
    protected $formData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->feedbackForm = $this->buildMockOfClass(FeedbackForm::class);
        $this->feedbackFormRepository = $this->buildMockOfInterface(FeedbackFormRepository::class);
        $this->feedbackFormRepository->expects($this->any())
            ->method('ofId')
            ->with($this->firmId, $this->feedbackFormId)
            ->willReturn($this->feedbackForm);

        $this->service = new FeedbackFormUpdate($this->feedbackFormRepository);
        $this->formData = $this->buildMockOfClass(FormData::class);
        $this->formData->expects($this->any())
                ->method('getName')
                ->willReturn('new form name');
    }

    protected function execute()
    {
        $this->service->execute($this->firmId, $this->feedbackFormId, $this->formData);
    }
    public function test_execute_updateFeedbackForm()
    {
        $this->feedbackForm->expects($this->once())
            ->method('update')
            ->with($this->formData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->feedbackFormRepository->expects($this->once())
            ->method('update');
        $this->execute();
    }
    
}

<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\Firm\FeedbackForm;
use Tests\TestBase;

class FeedbackFormRemoveTest extends TestBase
{
    protected $service;
    protected $feedbackFormRepository, $feedbackForm, $firmId = 'firm-id',
        $feedbackFormId = 'consultation-feedback-form-id';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->feedbackForm = $this->buildMockOfClass(FeedbackForm::class);
        $this->feedbackFormRepository = $this->buildMockOfInterface(FeedbackFormRepository::class);
        $this->feedbackFormRepository->expects($this->any())
            ->method('ofId')
            ->with($this->firmId, $this->feedbackFormId)
            ->willReturn($this->feedbackForm);
        
        $this->service = new FeedbackFormRemove($this->feedbackFormRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->feedbackFormId);
    }
    
    public function test_remove_removeFeedbackForm()
    {
        $this->feedbackForm->expects($this->once())
            ->method('remove');
        $this->execute();
    }
    public function test_remove_updateRepository()
    {
        $this->feedbackFormRepository->expects($this->once())
            ->method('update');
        $this->execute();
    }
    
}

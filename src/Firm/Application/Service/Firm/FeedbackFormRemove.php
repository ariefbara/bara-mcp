<?php

namespace Firm\Application\Service\Firm;

class FeedbackFormRemove
{
    protected $feedbackFormRepository;
    
    function __construct(FeedbackFormRepository $feedbackFormRepository)
    {
        $this->feedbackFormRepository = $feedbackFormRepository;
    }
    
    public function execute($firmId, $feedbackFormId): void
    {
        $this->feedbackFormRepository->ofId($firmId, $feedbackFormId)
            ->remove();
        $this->feedbackFormRepository->update();
    }

}

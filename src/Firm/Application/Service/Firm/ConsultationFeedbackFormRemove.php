<?php

namespace Firm\Application\Service\Firm;

class ConsultationFeedbackFormRemove
{
    protected $consultationFeedbackFormRepository;
    
    function __construct(ConsultationFeedbackFormRepository $consultationFeedbackFormRepository)
    {
        $this->consultationFeedbackFormRepository = $consultationFeedbackFormRepository;
    }
    
    public function execute($firmId, $consultationFeedbackFormId): void
    {
        $this->consultationFeedbackFormRepository->ofId($firmId, $consultationFeedbackFormId)
            ->remove();
        $this->consultationFeedbackFormRepository->update();
    }

}

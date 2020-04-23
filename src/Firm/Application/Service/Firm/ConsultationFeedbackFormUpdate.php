<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\ {
    Firm\ConsultationFeedbackForm,
    Shared\FormData
};

class ConsultationFeedbackFormUpdate
{

    protected $consultationFeedbackFormRepository;

    function __construct(ConsultationFeedbackFormRepository $consultationFeedbackFormRepository)
    {
        $this->consultationFeedbackFormRepository = $consultationFeedbackFormRepository;
    }

    public function execute(string $firmId, $consultationFeedbackFormId, FormData $formData): ConsultationFeedbackForm
    {
        $consultationFeedbackForm = $this->consultationFeedbackFormRepository->ofId($firmId, $consultationFeedbackFormId);
        $consultationFeedbackForm->update($formData);
        $this->consultationFeedbackFormRepository->update();
        return $consultationFeedbackForm;
    }

}

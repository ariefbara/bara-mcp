<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\ {
    Firm\FeedbackForm,
    Shared\FormData
};

class FeedbackFormUpdate
{

    protected $feedbackFormRepository;

    function __construct(FeedbackFormRepository $feedbackFormRepository)
    {
        $this->feedbackFormRepository = $feedbackFormRepository;
    }

    public function execute(string $firmId, $feedbackFormId, FormData $formData): void
    {
        $feedbackForm = $this->feedbackFormRepository->ofId($firmId, $feedbackFormId);
        $feedbackForm->update($formData);
        $this->feedbackFormRepository->update();
    }

}

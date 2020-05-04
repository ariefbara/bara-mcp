<?php

namespace Firm\Application\Service\Firm;

use Firm\ {
    Application\Service\FirmRepository,
    Domain\Model\Firm\FeedbackForm,
    Domain\Model\Shared\Form,
    Domain\Model\Shared\FormData
};

class FeedbackFormAdd
{

    protected $feedbackFormRepository;
    protected $firmRepository;

    function __construct(
        FeedbackFormRepository $feedbackFormRepository, FirmRepository $firmRepository)
    {
        $this->feedbackFormRepository = $feedbackFormRepository;
        $this->firmRepository = $firmRepository;
    }

    public function execute(string $firmId, FormData $formData): string
    {
        $firm = $this->firmRepository->ofId($firmId);
        $id = $this->feedbackFormRepository->nextIdentity();
        $form = new Form($id, $formData);

        $feedbackForm = new FeedbackForm($firm, $id, $form);
        $this->feedbackFormRepository->add($feedbackForm);

        return $id;
    }

}

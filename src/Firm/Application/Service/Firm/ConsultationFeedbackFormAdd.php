<?php

namespace Firm\Application\Service\Firm;

use Firm\ {
    Application\Service\FirmRepository,
    Domain\Model\Firm\ConsultationFeedbackForm,
    Domain\Model\Shared\Form,
    Domain\Model\Shared\FormData
};

class ConsultationFeedbackFormAdd
{

    protected $consultationFeedbackFormRepository;
    protected $firmRepository;

    function __construct(
        ConsultationFeedbackFormRepository $consultationFeedbackFormRepository, FirmRepository $firmRepository)
    {
        $this->consultationFeedbackFormRepository = $consultationFeedbackFormRepository;
        $this->firmRepository = $firmRepository;
    }

    public function execute(string $firmId, FormData $formData): ConsultationFeedbackForm
    {
        $firm = $this->firmRepository->ofId($firmId);
        $id = $this->consultationFeedbackFormRepository->nextIdentity();
        $form = new Form($id, $formData);

        $consultationFeedbackForm = new ConsultationFeedbackForm($firm, $id, $form);
        $this->consultationFeedbackFormRepository->add($consultationFeedbackForm);

        return $consultationFeedbackForm;
    }

}

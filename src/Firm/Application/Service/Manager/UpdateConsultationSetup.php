<?php

namespace Firm\Application\Service\Manager;

class UpdateConsultationSetup
{

    /**
     * 
     * @var ConsultationSetupRepository
     */
    protected $consultationSetupRepository;

    /**
     * 
     * @var ManagerRepository
     */
    protected $managerRepository;

    /**
     * 
     * @var FeedbackFormRepository
     */
    protected $feedbackFormRepository;

    function __construct(
            ConsultationSetupRepository $consultationSetupRepository, ManagerRepository $managerRepository,
            FeedbackFormRepository $feedbackFormRepository)
    {
        $this->consultationSetupRepository = $consultationSetupRepository;
        $this->managerRepository = $managerRepository;
        $this->feedbackFormRepository = $feedbackFormRepository;
    }

    public function execute(
            string $firmId, string $managerId, string $consultationSetupId, string $name, int $sessionDuration,
            string $participantFeedbackFormId, string $consultantFeedbackFormId): void
    {
        $consultationSetup = $this->consultationSetupRepository->aConsultationSetupOfId($consultationSetupId);
        $participantFeedbackForm = $this->feedbackFormRepository->aFeedbackFormOfId($participantFeedbackFormId);
        $consultantFeedbackForm = $this->feedbackFormRepository->aFeedbackFormOfId($consultantFeedbackFormId);
        
        $this->managerRepository->aManagerInFirm($firmId, $managerId)->updateConsultationSetup(
                $consultationSetup, $name, $sessionDuration, $participantFeedbackForm, $consultantFeedbackForm);
        $this->consultationSetupRepository->update();
    }

}

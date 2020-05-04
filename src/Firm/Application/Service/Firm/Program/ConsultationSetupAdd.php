<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\{
    Application\Service\Firm\FeedbackFormRepository,
    Application\Service\Firm\ProgramRepository,
    Domain\Model\Firm\Program\ConsultationSetup
};

class ConsultationSetupAdd
{

    /**
     *
     * @var ConsultationSetupRepository
     */
    protected $consultationSetupRepository;

    /**
     *
     * @var ProgramRepository
     */
    protected $programRepository;

    /**
     *
     * @var FeedbackFormRepository
     */
    protected $feedbackFormRepository;

    function __construct(ConsultationSetupRepository $consultationSetupRepository, ProgramRepository $programRepository,
            FeedbackFormRepository $feedbackFormRepository)
    {
        $this->consultationSetupRepository = $consultationSetupRepository;
        $this->programRepository = $programRepository;
        $this->feedbackFormRepository = $feedbackFormRepository;
    }

    public function execute(
            string $firmId, string $programId, string $name, int $sessionDuration, string $participantFeedbacFormId,
            string $mentorFeedbackFormId): string
    {
        $program = $this->programRepository->ofId($firmId, $programId);
        $id = $this->consultationSetupRepository->nextIdentity();
        $participantFeedbackForm = $this->feedbackFormRepository
                ->ofId($firmId, $participantFeedbacFormId);
        $mentorFeedbackForm = $this->feedbackFormRepository
                ->ofId($firmId, $mentorFeedbackFormId);

        $consultationSetup = new ConsultationSetup($program, $id, $name, $sessionDuration, $participantFeedbackForm,
                $mentorFeedbackForm);
        $this->consultationSetupRepository->add($consultationSetup);

        return $id;
    }

}

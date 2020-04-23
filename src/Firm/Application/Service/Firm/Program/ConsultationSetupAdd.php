<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\{
    Application\Service\Firm\ConsultationFeedbackFormRepository,
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
     * @var ConsultationFeedbackFormRepository
     */
    protected $consultationFeedbackFormRepository;

    function __construct(ConsultationSetupRepository $consultationSetupRepository, ProgramRepository $programRepository,
            ConsultationFeedbackFormRepository $consultationFeedbackFormRepository)
    {
        $this->consultationSetupRepository = $consultationSetupRepository;
        $this->programRepository = $programRepository;
        $this->consultationFeedbackFormRepository = $consultationFeedbackFormRepository;
    }

    public function execute(
            string $firmId, string $programId, string $name, int $sessionDuration, string $participantFeedbacFormId,
            string $mentorFeedbackFormId): ConsultationSetup
    {
        $program = $this->programRepository->ofId($firmId, $programId);
        $id = $this->consultationSetupRepository->nextIdentity();
        $participantFeedbackForm = $this->consultationFeedbackFormRepository
                ->ofId($firmId, $participantFeedbacFormId);
        $mentorFeedbackForm = $this->consultationFeedbackFormRepository
                ->ofId($firmId, $mentorFeedbackFormId);

        $consultationSetup = new ConsultationSetup($program, $id, $name, $sessionDuration, $participantFeedbackForm,
                $mentorFeedbackForm);
        $this->consultationSetupRepository->add($consultationSetup);

        return $consultationSetup;
    }

}

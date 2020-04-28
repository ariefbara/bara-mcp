<?php

namespace Client\Application\Service\Client\ProgramParticipation\ConsultationSession;

use Client\Application\Service\Client\ProgramParticipation\{
    ConsultationSessionRepository,
    ProgramParticipationCompositionId
};
use Shared\Domain\Model\FormRecordData;

class ParticipantFeedbackSet
{

    /**
     *
     * @var ConsultationSessionRepository
     */
    protected $consultationSessionRepository;

    function __construct(ConsultationSessionRepository $consultationSessionRepository)
    {
        $this->consultationSessionRepository = $consultationSessionRepository;
    }

    public function execute(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $consultationSessionId,
            FormRecordData $formRecordData): void
    {
        $this->consultationSessionRepository->ofId($programParticipationCompositionId, $consultationSessionId)
                ->setParticipantFeedback($formRecordData);
        $this->consultationSessionRepository->update();
    }

}

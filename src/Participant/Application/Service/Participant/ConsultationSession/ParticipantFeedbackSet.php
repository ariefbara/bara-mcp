<?php

namespace Participant\Application\Service\Participant\ConsultationSession;

use Participant\Application\Service\Participant\ConsultationSessionRepository;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

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
            string $firmId, string $clientId, string $programId, string $consultationSessionId,
            FormRecordData $formRecordData): void
    {
        $this->consultationSessionRepository
                ->aConsultationSessionOfClientParticipant($firmId, $clientId, $programId, $consultationSessionId)
                ->setParticipantFeedback($formRecordData);
        $this->consultationSessionRepository->update();
    }

}

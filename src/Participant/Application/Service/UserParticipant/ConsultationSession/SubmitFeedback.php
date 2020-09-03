<?php

namespace Participant\Application\Service\UserParticipant\ConsultationSession;

use Participant\Application\Service\Participant\ConsultationSessionRepository;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class SubmitFeedback
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
            string $userId, string $programId, string $consultationSessionId,
            FormRecordData $formRecordData): void
    {
        $this->consultationSessionRepository
                ->aConsultationSessionOfUserParticipant($userId, $programId, $consultationSessionId)
                ->setParticipantFeedback($formRecordData);
        $this->consultationSessionRepository->update();
    }
}

<?php

namespace Query\Application\Service\Client\ProgramParticipation;

use Client\Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId;
use Query\Domain\Model\Firm\Program\Participant\ConsultationSession;

class ConsultationSessionView
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

    /**
     * 
     * @param ProgramParticipationCompositionId $programParticipationCompositionId
     * @param int $page
     * @param int $pageSize
     * @return ConsultationSession[]
     */
    public function showAll(
            ProgramParticipationCompositionId $programParticipationCompositionId, int $page, int $pageSize)
    {
        return $this->consultationSessionRepository->allConsultationSessionsOfParticipant(
                        $programParticipationCompositionId, $page, $pageSize);
    }

    public function showById(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $consultationSessionId): ConsultationSession
    {
        return $this->consultationSessionRepository->aConsultationSessionOfParticipant(
                        $programParticipationCompositionId, $consultationSessionId);
    }

}

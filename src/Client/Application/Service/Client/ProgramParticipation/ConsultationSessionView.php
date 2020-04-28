<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\Domain\Model\Client\ProgramParticipation\ConsultationSession;

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

    public function showById(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $consultationSessionId): ConsultationSession
    {
        return $this->consultationSessionRepository->ofId($programParticipationCompositionId, $consultationSessionId);
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
        return $this->consultationSessionRepository->all($programParticipationCompositionId, $page, $pageSize);
    }

}

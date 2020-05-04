<?php

namespace Query\Application\Service\Firm\Program\Participant;

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

    public function showById(ParticipantCompositionId $participantCompositionId, string $consultationSessionId): ConsultationSession
    {
        return $this->consultationSessionRepository->ofId($participantCompositionId, $consultationSessionId);
    }

    /**
     * 
     * @param ParticipantCompositionId $participantCompositionId
     * @param int $page
     * @param int $pageSize
     * @return ConsultationSession[]
     */
    public function showAll(ParticipantCompositionId $participantCompositionId, int $page, int $pageSize)
    {
        return $this->consultationSessionRepository->all($participantCompositionId, $page, $pageSize);
    }

}

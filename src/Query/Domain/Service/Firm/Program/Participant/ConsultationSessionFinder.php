<?php

namespace Query\Domain\Service\Firm\Program\Participant;

use Query\{
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession,
    Domain\Model\Firm\Team,
    Infrastructure\QueryFilter\ConsultationSessionFilter
};

class ConsultationSessionFinder
{

    /**
     *
     * @var ConsultationSessionRepository
     */
    protected $consultationSessionRepository;

    public function __construct(ConsultationSessionRepository $consultationSessionRepository)
    {
        $this->consultationSessionRepository = $consultationSessionRepository;
    }

    public function findConsultationSessionBelongsToTeam(Team $team, string $teamProgramParticipationId,
            string $consultationSessionId): ConsultationSession
    {
        return $this->consultationSessionRepository->aConsultationSessionBelongsToTeam(
                        $team->getId(), $teamProgramParticipationId, $consultationSessionId);
    }

    public function findAllConsultationSessionBelongsToTeam(
            Team $team, string $teamProgramParticipationId, int $page, int $pageSize,
            ?ConsultationSessionFilter $consultationSessionFilter)
    {
        return $this->consultationSessionRepository->allConsultationSessionsBelongsToTeam(
                        $team->getId(), $teamProgramParticipationId, $page, $pageSize, $consultationSessionFilter);
    }

}

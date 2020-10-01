<?php

namespace Query\Domain\Service\Firm\Program\ConsultationSetup;

use Query\{
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest,
    Domain\Model\Firm\Program\Participant,
    Domain\Model\Firm\Team,
    Infrastructure\QueryFilter\ConsultationRequestFilter
};

class ConsultationRequestFinder
{

    /**
     *
     * @var ConsultationRequestRepository
     */
    protected $consultationRequestRepository;

    public function __construct(ConsultationRequestRepository $consultationRequestRepository)
    {
        $this->consultationRequestRepository = $consultationRequestRepository;
    }

    public function findConsultationRequestBelongsToTeam(
            Team $team, string $teamProgramParticipationId, string $consultationRequestId): ConsultationRequest
    {
        $teamId = $team->getId();
        return $this->consultationRequestRepository->aConsultationRequestBelongsToTeam(
                        $teamId, $teamProgramParticipationId, $consultationRequestId);
    }

    public function findAllConsultationRequestsBelongsToTeam(
            Team $team, string $teamProgramParticipationId, int $page, int $pageSize,
            ?ConsultationRequestFilter $consultationRequestFilter)
    {
        $teamId = $team->getId();
        return $this->consultationRequestRepository->allConsultationRequestsBelongsToTeam(
                        $teamId, $teamProgramParticipationId, $page, $pageSize, $consultationRequestFilter);
    }

}

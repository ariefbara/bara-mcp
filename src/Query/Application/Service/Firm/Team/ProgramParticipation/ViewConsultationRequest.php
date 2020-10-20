<?php

namespace Query\Application\Service\Firm\Team\ProgramParticipation;

use Query\{
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest,
    Infrastructure\QueryFilter\ConsultationRequestFilter
};

class ViewConsultationRequest
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

    /**
     * 
     * @param string $teamId
     * @param string $teamProgramParticipationId
     * @param int $page
     * @param int $pageSize
     * @param ConsultationRequestFilter|null $consultationRequestFilter
     * @return ConsultationRequest[]
     */
    public function showAll(
            string $teamId, string $teamProgramParticipationId, int $page, int $pageSize,
            ?ConsultationRequestFilter $consultationRequestFilter)
    {
        return $this->consultationRequestRepository->allConsultationRequestsBelongsInProgramParticipationOfTeam(
                        $teamId, $teamProgramParticipationId, $page, $pageSize, $consultationRequestFilter);
    }

    public function showById(string $teamId, string $consultationRequestId): ConsultationRequest
    {
        return $this->consultationRequestRepository->aConsultationRequestBelongsToTeam($teamId, $consultationRequestId);
    }

}

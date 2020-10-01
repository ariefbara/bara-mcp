<?php

namespace Query\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use Query\{
    Application\Service\Firm\Client\TeamMembershipRepository,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest,
    Domain\Service\Firm\Program\ConsultationSetup\ConsultationRequestFinder,
    Infrastructure\QueryFilter\ConsultationRequestFilter
};

class ViewConsultationRequest
{

    /**
     *
     * @var TeamMembershipRepository
     */
    protected $teamMembershipRepository;

    /**
     *
     * @var ConsultationRequestFinder
     */
    protected $consultationRequestFinder;

    public function __construct(TeamMembershipRepository $teamMembershipRepository,
            ConsultationRequestFinder $consultationRequestFinder)
    {
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->consultationRequestFinder = $consultationRequestFinder;
    }

    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param string $teamMembershipId
     * @param int $page
     * @param int $pageSize
     * @param ConsultationRequestFilter|null $consultationRequestFilter
     * @return ConsultationRequest
     */
    public function showAll(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId, int $page,
            int $pageSize, ?ConsultationRequestFilter $consultationRequestFilter)
    {
        return $this->teamMembershipRepository
                        ->aTeamMembershipOfClient($firmId, $clientId, $teamMembershipId)
                        ->viewAllConsultationRequest(
                                $this->consultationRequestFinder, $teamProgramParticipationId, $page, $pageSize,
                                $consultationRequestFilter);
    }

    public function showById(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId,
            string $consultationRequestId): ConsultationRequest
    {
        return $this->teamMembershipRepository
                        ->aTeamMembershipOfClient($firmId, $clientId, $teamMembershipId)
                        ->viewConsultationRequest(
                                $this->consultationRequestFinder, $teamProgramParticipationId, $consultationRequestId);
    }

}

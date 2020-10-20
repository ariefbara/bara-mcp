<?php

namespace Query\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use Query\ {
    Application\Service\Firm\Client\TeamMembershipRepository,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession,
    Domain\Service\Firm\Program\Participant\ConsultationSessionFinder,
    Infrastructure\QueryFilter\ConsultationSessionFilter
};

class ViewConsultationSession
{

    /**
     *
     * @var TeamMembershipRepository
     */
    protected $teamMembershipRepository;

    /**
     *
     * @var ConsultationSessionFinder
     */
    protected $consultationSessionFinder;

    public function __construct(TeamMembershipRepository $teamMembershipRepository,
            ConsultationSessionFinder $consultationSessionFinder)
    {
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->consultationSessionFinder = $consultationSessionFinder;
    }

    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param string $teamMembershipId
     * @param string $teamProgramParticipationId
     * @param int $page
     * @param int $pageSize
     * @param ConsultationSessionFilter|null $consultationSessionFilter
     * @return ConsultationSession[]
     */
    public function showAll(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId, int $page,
            int $pageSize, ?ConsultationSessionFilter $consultationSessionFilter)
    {
        return $this->teamMembershipRepository
                        ->aTeamMembershipOfClient($firmId, $clientId, $teamMembershipId)
                        ->viewAllConsultationSession(
                                $this->consultationSessionFinder, $teamProgramParticipationId, $page, $pageSize,
                                $consultationSessionFilter);
    }

    public function showById(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId,
            string $consultationSessionId): ConsultationSession
    {
        return $this->teamMembershipRepository
                        ->aTeamMembershipOfClient($firmId, $clientId, $teamMembershipId)
                        ->viewConsultationSession(
                                $this->consultationSessionFinder, $teamProgramParticipationId, $consultationSessionId);
    }

}

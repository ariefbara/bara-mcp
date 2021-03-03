<?php

namespace Query\Application\Service\TeamMember;

use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Query\Domain\Service\ObjectiveProgressReportFinder;

class ViewObjectiveProgressReport
{

    /**
     * 
     * @var TeamMemberRepository
     */
    protected $teamMemberRepository;

    /**
     * 
     * @var TeamParticipantRepository
     */
    protected $teamParticipantRepository;

    /**
     * 
     * @var ObjectiveProgressReportFinder
     */
    protected $objectiveProgressReportFinder;

    public function __construct(TeamMemberRepository $teamMemberRepository,
            TeamParticipantRepository $teamParticipantRepository,
            ObjectiveProgressReportFinder $objectiveProgressReportFinder)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->teamParticipantRepository = $teamParticipantRepository;
        $this->objectiveProgressReportFinder = $objectiveProgressReportFinder;
    }

    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param string $teamId
     * @param string $programParticipationId
     * @param string $objectiveId
     * @param int $page
     * @param int $pageSize
     * @return ObjectiveProgressReport[]
     */
    public function showAll(
            string $firmId, string $clientId, string $teamId, string $programParticipationId, string $objectiveId,
            int $page, int $pageSize)
    {
        $teamParticipant = $this->teamParticipantRepository->ofId($programParticipationId);
        return $this->teamMemberRepository->aTeamMemberOfClientCorrespondWithTeam($firmId, $clientId, $teamId)
                        ->viewAllObjectiveProgressReportInObjective(
                                $this->objectiveProgressReportFinder, $teamParticipant, $objectiveId, $page, $pageSize);
    }

    public function showById(
            string $firmId, string $clientId, string $teamId, string $programParticipationId,
            string $objectiveProgressReportId): ObjectiveProgressReport
    {
        $teamParticipant = $this->teamParticipantRepository->ofId($programParticipationId);
        return $this->teamMemberRepository->aTeamMemberOfClientCorrespondWithTeam($firmId, $clientId, $teamId)
                        ->viewObjectiveProgressReport(
                                $this->objectiveProgressReportFinder, $teamParticipant, $objectiveProgressReportId);
    }

}

<?php

namespace Query\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use Query\{
    Application\Service\Firm\Client\TeamMembershipRepository,
    Domain\Model\Firm\Program\Participant\Worksheet,
    Domain\Service\Firm\Program\Participant\WorksheetFinder,
    Domain\Service\Firm\Team\TeamProgramParticipationFinder
};

class ViewWorksheet
{

    /**
     *
     * @var TeamMembershipRepository
     */
    protected $teamMembershipRepository;

    /**
     *
     * @var TeamProgramParticipationFinder
     */
    protected $teamProgramParticipationFinder;

    /**
     *
     * @var WorksheetFinder
     */
    protected $worksheetFinder;

    public function __construct(TeamMembershipRepository $teamMembershipRepository,
            TeamProgramParticipationFinder $teamProgramParticipationFinder, WorksheetFinder $worksheetFinder)
    {
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->teamProgramParticipationFinder = $teamProgramParticipationFinder;
        $this->worksheetFinder = $worksheetFinder;
    }

    public function showById(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId,
            string $worksheetId): Worksheet
    {
        return $this->teamMembershipRepository
                        ->aTeamMembershipOfClient($firmId, $clientId, $teamMembershipId)
                        ->viewWorksheet(
                                $this->teamProgramParticipationFinder, $teamProgramParticipationId,
                                $this->worksheetFinder, $worksheetId);
    }

    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param string $teamMembershipId
     * @param string $teamProgramParticipationId
     * @param int $page
     * @param int $pageSize
     * @return Worksheet[]
     */
    public function showAll(string $firmId, string $clientId, string $teamMembershipId,
            string $teamProgramParticipationId, int $page, int $pageSize)
    {
        return $this->teamMembershipRepository
                        ->aTeamMembershipOfClient($firmId, $clientId, $teamMembershipId)
                        ->viewAllWorksheets(
                                $this->teamProgramParticipationFinder, $teamProgramParticipationId,
                                $this->worksheetFinder, $page, $pageSize);
    }

    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param string $teamMembershipId
     * @param string $teamProgramParticipationId
     * @param int $page
     * @param int $pageSize
     * @return Worksheet[]
     */
    public function showAllRoots(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId, int $page,
            int $pageSize)
    {
        return $this->teamMembershipRepository
                        ->aTeamMembershipOfClient($firmId, $clientId, $teamMembershipId)
                        ->viewAllRootWorksheets($this->teamProgramParticipationFinder, $teamProgramParticipationId,
                                $this->worksheetFinder, $page, $pageSize);
    }

    /**
     * 
     * @param type $firmId
     * @param type $clientId
     * @param type $teamMembershipId
     * @param type $teamProgramParticipationId
     * @param type $worksheetId
     * @param type $page
     * @param type $pageSize
     * @return Worksheet[]
     */
    public function showAllBranches(
            $firmId, $clientId, $teamMembershipId, $teamProgramParticipationId, $worksheetId, $page, $pageSize)
    {
        return $this->teamMembershipRepository
                        ->aTeamMembershipOfClient($firmId, $clientId, $teamMembershipId)
                        ->viewAllBranchWorksheets(
                                $this->teamProgramParticipationFinder, $teamProgramParticipationId,
                                $this->worksheetFinder, $worksheetId, $page, $pageSize);
    }

}

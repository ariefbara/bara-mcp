<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use Participant\Application\Service\{
    Firm\Client\TeamMembership\TeamProgramParticipationRepository,
    Firm\Client\TeamMembershipRepository,
    Firm\Program\MissionRepository,
    Participant\WorksheetRepository
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class SubmitBranchWorksheet
{

    /**
     *
     * @var WorksheetRepository
     */
    protected $worksheetRepository;

    /**
     *
     * @var TeamMembershipRepository
     */
    protected $teamMembershipRepository;

    /**
     *
     * @var TeamProgramParticipationRepository
     */
    protected $teamProgramParticipantRepository;

    /**
     *
     * @var MissionRepository
     */
    protected $missionRepository;

    public function __construct(
            WorksheetRepository $worksheetRepository, TeamMembershipRepository $teamMembershipRepository,
            TeamProgramParticipationRepository $teamProgramParticipantRepository, MissionRepository $missionRepository)
    {
        $this->worksheetRepository = $worksheetRepository;
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->teamProgramParticipantRepository = $teamProgramParticipantRepository;
        $this->missionRepository = $missionRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId,
            string $parentWorksheetId, string $missionId, string $name, FormRecordData $formRecordData): string
    {
        $teamProgramParticipation = $this->teamProgramParticipantRepository->ofId($teamProgramParticipationId);
        $parentWorksheet = $this->worksheetRepository->ofId($parentWorksheetId);
        $mission = $this->missionRepository->ofId($missionId);
        $id = $this->worksheetRepository->nextIdentity();

        $branchWorksheet = $this->teamMembershipRepository
                ->ofId($firmId, $clientId, $teamMembershipId)
                ->submitBranchWorksheet(
                $teamProgramParticipation, $parentWorksheet, $id, $name, $mission, $formRecordData);
        $this->worksheetRepository->add($branchWorksheet);
        return $id;
    }

}

<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use Participant\Application\Service\{
    Firm\Client\TeamMembership\TeamProgramParticipationRepository,
    Firm\Client\TeamMembershipRepository,
    Firm\Program\MissionRepository,
    Participant\WorksheetRepository
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class SubmitRootWorksheet
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
    protected $teamProgramParticipationRepository;

    /**
     *
     * @var MissionRepository
     */
    protected $missionRepository;

    public function __construct(WorksheetRepository $worksheetRepository,
            TeamMembershipRepository $teamMembershipRepository,
            TeamProgramParticipationRepository $teamProgramParticipationRepository, MissionRepository $missionRepository)
    {
        $this->worksheetRepository = $worksheetRepository;
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->teamProgramParticipationRepository = $teamProgramParticipationRepository;
        $this->missionRepository = $missionRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $teamMembershipId, string $programParticipationId,
            string $missionId, string $name, FormRecordData $formRecordData): string
    {
        $teamProgramParticipation = $this->teamProgramParticipationRepository->ofId($programParticipationId);
        $id = $this->worksheetRepository->nextIdentity();
        $mission = $this->missionRepository->ofId($missionId);
        
        $worksheet = $this->teamMembershipRepository->aTeamMembershipCorrespondWithTeam($firmId, $clientId, $teamMembershipId)
                ->submitRootWorksheet($teamProgramParticipation, $id, $name, $mission, $formRecordData);
        $this->worksheetRepository->add($worksheet);
        return $id;
    }

}

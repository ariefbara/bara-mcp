<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use Participant\Application\Service\{
    Firm\Client\TeamMembership\TeamProgramParticipationRepository,
    Firm\Client\TeamMembershipRepository,
    Participant\WorksheetRepository
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class UpdateWorksheet
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

    public function __construct(WorksheetRepository $worksheetRepository,
            TeamMembershipRepository $teamMembershipRepository,
            TeamProgramParticipationRepository $teamProgramParticipationRepository)
    {
        $this->worksheetRepository = $worksheetRepository;
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->teamProgramParticipationRepository = $teamProgramParticipationRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $teamMembershipId, string $programParticipationId,
            string $worksheetId, string $name, FormRecordData $formRecordData): void
    {
        $teamProgramParticipation = $this->teamProgramParticipationRepository->ofId($programParticipationId);
        $worksheet = $this->worksheetRepository->ofId($worksheetId);
        $this->teamMembershipRepository->ofId($firmId, $clientId, $teamMembershipId)
                ->updateWorksheet($teamProgramParticipation, $worksheet, $name, $formRecordData);
        $this->worksheetRepository->update();
    }

}

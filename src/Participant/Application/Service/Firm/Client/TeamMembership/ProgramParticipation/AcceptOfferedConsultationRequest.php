<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use Participant\Application\Service\Firm\Client\ {
    TeamMembership\TeamProgramParticipationRepository,
    TeamMembershipRepository
};

class AcceptOfferedConsultationRequest
{

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

    public function __construct(
            TeamMembershipRepository $teamMembershipRepository,
            TeamProgramParticipationRepository $teamProgramParticipationRepository)
    {
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->teamProgramParticipationRepository = $teamProgramParticipationRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $teamMembershipId, string $programParticipationId,
            string $consultationRequestId): void
    {
        $teamProgramParticipation = $this->teamProgramParticipationRepository->ofId($programParticipationId);
        $this->teamMembershipRepository->ofId($firmId, $clientId, $teamMembershipId)
                ->acceptOfferedConsultationRequest($teamProgramParticipation, $consultationRequestId);
        $this->teamProgramParticipationRepository->update();
    }

}

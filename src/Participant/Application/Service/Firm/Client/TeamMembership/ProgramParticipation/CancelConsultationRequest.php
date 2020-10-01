<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use Participant\Application\Service\{
    Firm\Client\TeamMembership\TeamProgramParticipationRepository,
    Firm\Client\TeamMembershipRepository,
    Participant\ConsultationRequestRepository
};

class CancelConsultationRequest
{

    /**
     *
     * @var ConsultationRequestRepository
     */
    protected $consultationRequestRepository;

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
            ConsultationRequestRepository $consultationRequestRepository,
            TeamMembershipRepository $teamMembershipRepository,
            TeamProgramParticipationRepository $teamProgramParticipationRepository)
    {
        $this->consultationRequestRepository = $consultationRequestRepository;
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->teamProgramParticipationRepository = $teamProgramParticipationRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $teamMembershipId, string $programParticipationId,
            string $consultationRequestId): void
    {
        $teamProgramParticipation = $this->teamProgramParticipationRepository->ofId($programParticipationId);
        $consultationRequest = $this->consultationRequestRepository->ofId($consultationRequestId);
        $this->teamMembershipRepository->ofId($firmId, $clientId, $teamMembershipId)
                ->cancelConsultationRequest($teamProgramParticipation, $consultationRequest);
        
        $this->consultationRequestRepository->update();
    }

}

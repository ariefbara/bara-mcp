<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation\ConsultationSession;

use Participant\Application\Service\{
    Firm\Client\TeamMembership\TeamProgramParticipationRepository,
    Firm\Client\TeamMembershipRepository,
    Participant\ConsultationSessionRepository
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class SubmitReport
{

    /**
     *
     * @var ConsultationSessionRepository
     */
    protected $consultationSessionRepository;

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

    public function __construct(ConsultationSessionRepository $consultationSessionRepository,
            TeamMembershipRepository $teamMembershipRepository,
            TeamProgramParticipationRepository $teamProgramParticipationRepository)
    {
        $this->consultationSessionRepository = $consultationSessionRepository;
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->teamProgramParticipationRepository = $teamProgramParticipationRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId,
            string $consultationSessionId, FormRecordData $formRecordData): void
    {
        $teamProgramParticipation = $this->teamProgramParticipationRepository->ofId($teamProgramParticipationId);
        $consultationSession = $this->consultationSessionRepository->ofId($consultationSessionId);
        $this->teamMembershipRepository->ofId($firmId, $clientId, $teamMembershipId)
                ->submitConsultationSessionReport($teamProgramParticipation, $consultationSession, $formRecordData);
        
        $this->consultationSessionRepository->update();
    }

}

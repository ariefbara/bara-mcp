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

    public function __construct(ConsultationSessionRepository $consultationSessionRepository,
            TeamMembershipRepository $teamMembershipRepository)
    {
        $this->consultationSessionRepository = $consultationSessionRepository;
        $this->teamMembershipRepository = $teamMembershipRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $teamMembershipId, string $consultationSessionId,
            FormRecordData $formRecordData): void
    {
        $consultationSession = $this->consultationSessionRepository->ofId($consultationSessionId);
        $this->teamMembershipRepository->ofId($firmId, $clientId, $teamMembershipId)
                ->submitConsultationSessionReport($consultationSession, $formRecordData);
        $this->consultationSessionRepository->update();
    }

}

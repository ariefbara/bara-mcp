<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use DateTimeImmutable;
use Participant\Application\Service\{
    Firm\Client\TeamMembership\TeamProgramParticipationRepository,
    Firm\Client\TeamMembershipRepository,
    Firm\Program\ConsultantRepository,
    Firm\Program\ConsultationSetupRepository,
    Participant\ConsultationRequestRepository
};

class SubmitConsultationRequest
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

    /**
     *
     * @var ConsultationSetupRepository
     */
    protected $consultationSetupRepository;

    /**
     *
     * @var ConsultantRepository
     */
    protected $consultantRepository;

    public function __construct(
            ConsultationRequestRepository $consultationRequestRepository,
            TeamMembershipRepository $teamMembershipRepository,
            TeamProgramParticipationRepository $teamProgramParticipationRepository,
            ConsultationSetupRepository $consultationSetupRepository, ConsultantRepository $consultantRepository)
    {
        $this->consultationRequestRepository = $consultationRequestRepository;
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->teamProgramParticipationRepository = $teamProgramParticipationRepository;
        $this->consultationSetupRepository = $consultationSetupRepository;
        $this->consultantRepository = $consultantRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $teamMembershipId, string $programParticipationId,
            string $consultationSetupId, string $consultantId, DateTimeImmutable $startTime): string
    {
        $teamProgramParticipation = $this->teamProgramParticipationRepository->ofId($programParticipationId);
        $id = $this->consultationRequestRepository->nextIdentity();
        $consultationSetup = $this->consultationSetupRepository->ofId($consultationSetupId);
        $consultant = $this->consultantRepository->ofId($consultantId);

        $consultationRequest = $this->teamMembershipRepository->ofId($firmId, $clientId, $teamMembershipId)
                ->submitConsultationRequest($teamProgramParticipation, $id, $consultationSetup, $consultant, $startTime);
        $this->consultationRequestRepository->add($consultationRequest);
        return $id;
    }

}

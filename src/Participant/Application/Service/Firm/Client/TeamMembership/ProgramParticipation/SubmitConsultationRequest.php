<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use Participant\Application\Service\Firm\Client\TeamMembership\TeamProgramParticipationRepository;
use Participant\Application\Service\Firm\Client\TeamMembershipRepository;
use Participant\Application\Service\Firm\Program\ConsultantRepository;
use Participant\Application\Service\Firm\Program\ConsultationSetupRepository;
use Participant\Application\Service\Participant\ConsultationRequestRepository;
use Participant\Domain\Model\Participant\ConsultationRequestData;
use Resources\Application\Event\Dispatcher;

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

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    public function __construct(
            ConsultationRequestRepository $consultationRequestRepository,
            TeamMembershipRepository $teamMembershipRepository,
            TeamProgramParticipationRepository $teamProgramParticipationRepository,
            ConsultationSetupRepository $consultationSetupRepository, ConsultantRepository $consultantRepository,
            Dispatcher $dispatcher)
    {
        $this->consultationRequestRepository = $consultationRequestRepository;
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->teamProgramParticipationRepository = $teamProgramParticipationRepository;
        $this->consultationSetupRepository = $consultationSetupRepository;
        $this->consultantRepository = $consultantRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $firmId, string $clientId, string $teamMembershipId, string $programParticipationId,
            string $consultationSetupId, string $consultantId, ConsultationRequestData $consultationRequestData): string
    {
        $teamProgramParticipation = $this->teamProgramParticipationRepository->ofId($programParticipationId);
        $id = $this->consultationRequestRepository->nextIdentity();
        $consultationSetup = $this->consultationSetupRepository->ofId($consultationSetupId);
        $consultant = $this->consultantRepository->ofId($consultantId);

        $teamMembership = $this->teamMembershipRepository->aTeamMembershipCorrespondWithTeam($firmId, $clientId, $teamMembershipId);
        $consultationRequest = $teamMembership->submitConsultationRequest(
                $teamProgramParticipation, $id, $consultationSetup, $consultant, $consultationRequestData);
        $this->consultationRequestRepository->add($consultationRequest);

        $this->dispatcher->dispatch($teamMembership);
        return $id;
    }

}

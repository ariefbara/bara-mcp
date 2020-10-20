<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use Participant\Application\Service\{
    Firm\Client\TeamMembershipRepository,
    Participant\ConsultationRequestRepository
};
use Resources\Application\Event\Dispatcher;

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
     * @var Dispatcher
     */
    protected $dispatcher;

    public function __construct(
            ConsultationRequestRepository $consultationRequestRepository,
            TeamMembershipRepository $teamMembershipRepository, Dispatcher $dispatcher)
    {
        $this->consultationRequestRepository = $consultationRequestRepository;
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $firmId, string $clientId, string $teamMembershipId, string $consultationRequestId): void
    {
        $consultationRequest = $this->consultationRequestRepository->ofId($consultationRequestId);
        $teamMembership = $this->teamMembershipRepository->aTeamMembershipCorrespondWithTeam($firmId, $clientId, $teamMembershipId);
        $teamMembership->cancelConsultationRequest($consultationRequest);
        $this->consultationRequestRepository->update();
        
        $this->dispatcher->dispatch($teamMembership);
    }

}

<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use DateTimeImmutable;
use Participant\Application\Service\Firm\Client\{
    TeamMembership\TeamProgramParticipationRepository,
    TeamMembershipRepository
};
use Resources\Application\Event\Dispatcher;

class ChangeConsultationRequestTime
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

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    public function __construct(
            TeamMembershipRepository $teamMembershipRepository,
            TeamProgramParticipationRepository $teamProgramParticipationRepository, Dispatcher $dispatcher)
    {
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->teamProgramParticipationRepository = $teamProgramParticipationRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $firmId, string $clientId, string $teamMembershipId, string $programParticipationId,
            string $consultationRequestId, DateTimeImmutable $startTime): void
    {
        $teamProgramParticipation = $this->teamProgramParticipationRepository->ofId($programParticipationId);
        $teamMembership = $this->teamMembershipRepository->aTeamMembershipCorrespondWithTeam($firmId, $clientId, $teamMembershipId);
        $teamMembership->changeConsultationRequestTime($teamProgramParticipation, $consultationRequestId, $startTime);
        $this->teamProgramParticipationRepository->update();
        
        $this->dispatcher->dispatch($teamMembership);
    }

}

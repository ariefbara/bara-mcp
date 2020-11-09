<?php

namespace ActivityCreator\Application\Service\TeamMember;

use ActivityCreator\Domain\service\ActivityDataProvider;
use Resources\Application\Event\Dispatcher;

class UpdateActivity
{

    /**
     *
     * @var ParticipantActivityRepository
     */
    protected $participantActivityRepository;

    /**
     *
     * @var TeamMemberRepository
     */
    protected $teamMemberRepository;

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(ParticipantActivityRepository $participantActivityRepository,
            TeamMemberRepository $teamMemberRepository, Dispatcher $dispatcher)
    {
        $this->participantActivityRepository = $participantActivityRepository;
        $this->teamMemberRepository = $teamMemberRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $firmId, string $clientId, string $teamId, string $participantActivityId,
            ActivityDataProvider $activityDataProvider): void
    {
        $participantActivity = $this->participantActivityRepository->ofId($participantActivityId);
        $this->teamMemberRepository->aTeamMembershipCorrespondWithTeam($firmId, $clientId, $teamId)
                ->updateActivity($participantActivity, $activityDataProvider);
        $this->participantActivityRepository->update();
        
        $this->dispatcher->dispatch($participantActivity);
    }

}

<?php

namespace ActivityCreator\Application\Service\TeamMember;

use ActivityCreator\{
    Application\Service\ActivityTypeRepository,
    Domain\service\ActivityDataProvider
};
use Resources\Application\Event\Dispatcher;

class InitiateActivity
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
     * @var TeamParticipantRepository
     */
    protected $teamParticipantRepository;

    /**
     *
     * @var ActivityTypeRepository
     */
    protected $activityTypeRepository;

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(
            ParticipantActivityRepository $participantActivityRepository, TeamMemberRepository $teamMemberRepository,
            TeamParticipantRepository $teamParticipantRepository, ActivityTypeRepository $activityTypeRepository,
            Dispatcher $dispatcher)
    {
        $this->participantActivityRepository = $participantActivityRepository;
        $this->teamMemberRepository = $teamMemberRepository;
        $this->teamParticipantRepository = $teamParticipantRepository;
        $this->activityTypeRepository = $activityTypeRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $firmId, string $clientId, string $teamId, string $programParticipationId, string $activityTypeId,
            ActivityDataProvider $activityDataProvider): string
    {
        $programParticipation = $this->teamParticipantRepository->ofId($programParticipationId);
        $id = $this->participantActivityRepository->nextIdentity();
        $activityType = $this->activityTypeRepository->ofId($activityTypeId);
        
        $participantActivity = $this->teamMemberRepository
                ->aTeamMembershipCorrespondWithTeam($firmId, $clientId, $teamId)
                ->initiateActivityInProgramParticipation($programParticipation, $id, $activityType, $activityDataProvider);
        $this->participantActivityRepository->add($participantActivity);
        
        $this->dispatcher->dispatch($participantActivity);
        
        return $id;
    }

}

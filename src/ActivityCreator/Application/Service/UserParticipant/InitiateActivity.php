<?php

namespace ActivityCreator\Application\Service\UserParticipant;

use ActivityCreator\Application\Service\ActivityTypeRepository;
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
     * @var UserParticipantRepository
     */
    protected $userParticipantRepository;

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
            ParticipantActivityRepository $participantActivityRepository,
            UserParticipantRepository $userParticipantRepository, ActivityTypeRepository $activityTypeRepository,
            Dispatcher $dispatcher)
    {
        $this->participantActivityRepository = $participantActivityRepository;
        $this->userParticipantRepository = $userParticipantRepository;
        $this->activityTypeRepository = $activityTypeRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $userId, string $programParticipationId, string $activityTypeId, $activityDataProvider): string
    {
        $id = $this->participantActivityRepository->nextIdentity();
        $activityType = $this->activityTypeRepository->ofId($activityTypeId);

        $participantActivity = $this->userParticipantRepository
                ->aProgramParticipationBelongsToUser($userId, $programParticipationId)
                ->initiateActivity($id, $activityType, $activityDataProvider);
        $this->participantActivityRepository->add($participantActivity);

        $this->dispatcher->dispatch($participantActivity);

        return $id;
    }

}

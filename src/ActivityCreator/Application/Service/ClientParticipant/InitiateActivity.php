<?php

namespace ActivityCreator\Application\Service\ClientParticipant;

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
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;

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
            ClientParticipantRepository $clientParticipantRepository, ActivityTypeRepository $activityTypeRepository,
            Dispatcher $dispatcher)
    {
        $this->participantActivityRepository = $participantActivityRepository;
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->activityTypeRepository = $activityTypeRepository;
        $this->dispatcher = $dispatcher;
    }
    
    public function execute(string $firmId, string $clientId, string $programParticipationId, string $activityTypeId, $activityDataProvider): string
    {
        $id = $this->participantActivityRepository->nextIdentity();
        $activityType = $this->activityTypeRepository->ofId($activityTypeId);
        
        $participantActivity = $this->clientParticipantRepository
                ->aProgramParticipationBelongsToClient($firmId, $clientId, $programParticipationId)
                ->initiateActivity($id, $activityType, $activityDataProvider);
        $this->participantActivityRepository->add($participantActivity);
        
        $this->dispatcher->dispatch($participantActivity);
        
        return $id;
    }

}

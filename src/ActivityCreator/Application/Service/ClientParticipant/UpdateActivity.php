<?php

namespace ActivityCreator\Application\Service\ClientParticipant;

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
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(ParticipantActivityRepository $activityRepository, Dispatcher $dispatcher)
    {
        $this->participantActivityRepository = $activityRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $firmId, string $clientId, string $participantActivityId, $activityDataProvider): void
    {
        $participantActivity = $this->participantActivityRepository
                ->aParticipantActivityBelongsToClient($firmId, $clientId, $participantActivityId);
        $participantActivity->update($activityDataProvider);

        $this->participantActivityRepository->update();

        $this->dispatcher->dispatch($participantActivity);
    }
}

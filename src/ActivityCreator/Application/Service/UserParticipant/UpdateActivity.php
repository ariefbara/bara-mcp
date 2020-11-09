<?php

namespace ActivityCreator\Application\Service\UserParticipant;

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

    function __construct(ParticipantActivityRepository $participantActivityRepository, Dispatcher $dispatcher)
    {
        $this->participantActivityRepository = $participantActivityRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $userId, string $userParticipantActivityId, $activityDataProvider): void
    {
        $participantActivity = $this->participantActivityRepository
                ->aParticipantActivityBelongsToUser($userId, $userParticipantActivityId);
        $participantActivity->update($activityDataProvider);

        $this->participantActivityRepository->update();

        $this->dispatcher->dispatch($participantActivity);
    }
}

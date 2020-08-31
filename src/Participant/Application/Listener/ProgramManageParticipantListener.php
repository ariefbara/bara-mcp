<?php

namespace User\Application\Listener;

use User\Application\Service\User\ProgramParticipation\ParticipantNotificationAdd;
use Resources\Application\Event\{
    Event,
    Listener
};

class ProgramManageParticipantListener implements Listener
{

    /**
     *
     * @var UserNotificationRepository
     */
    protected $userNotificationRepository;

    /**
     *
     * @var ProgramParticipationRepository
     */
    protected $programParticipationRepository;

    function __construct(UserNotificationRepository $userNotificationRepository,
            ProgramParticipationRepository $programParticipationRepository)
    {
        $this->userNotificationRepository = $userNotificationRepository;
        $this->programParticipationRepository = $programParticipationRepository;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    private function execute(ProgramManageParticipantEventInterface $event): void
    {
        $id = $this->userNotificationRepository->nextIdentity();
        $userNotification = $this->programParticipationRepository
                ->aParticipantOfProgram(
                        $event->getFirmId(), $event->getProgramId(), $event->getParticipantId())
                ->createUserNotification($id, $event->getMessageForUser());
        $this->userNotificationRepository->add($userNotification);
    }

}

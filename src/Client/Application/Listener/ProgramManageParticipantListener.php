<?php

namespace Client\Application\Listener;

use Client\Application\Service\Client\ProgramParticipation\ParticipantNotificationAdd;
use Resources\Application\Event\{
    Event,
    Listener
};

class ProgramManageParticipantListener implements Listener
{

    /**
     *
     * @var ClientNotificationRepository
     */
    protected $clientNotificationRepository;

    /**
     *
     * @var ProgramParticipationRepository
     */
    protected $programParticipationRepository;

    function __construct(ClientNotificationRepository $clientNotificationRepository,
            ProgramParticipationRepository $programParticipationRepository)
    {
        $this->clientNotificationRepository = $clientNotificationRepository;
        $this->programParticipationRepository = $programParticipationRepository;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    private function execute(ProgramManageParticipantEventInterface $event): void
    {
        $id = $this->clientNotificationRepository->nextIdentity();
        $clientNotification = $this->programParticipationRepository
                ->aParticipantOfProgram(
                        $event->getFirmId(), $event->getProgramId(), $event->getParticipantId())
                ->createClientNotification($id, $event->getMessageForClient());
        $this->clientNotificationRepository->add($clientNotification);
    }

}

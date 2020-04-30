<?php

namespace Client\Application\Listener;

use Client\Application\Service\Client\ProgramParticipation\ParticipantNotificationAdd;
use Resources\Application\Event\{
    Event,
    Listener
};

class ParticipantNotificationListener implements Listener
{

    /**
     *
     * @var ParticipantNotificationAdd
     */
    protected $participantNotificationAdd;

    function __construct(ParticipantNotificationAdd $participantNotificationAdd)
    {
        $this->participantNotificationAdd = $participantNotificationAdd;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    private function execute(ParticipantNotificationEventInterface $event): void
    {
        $this->participantNotificationAdd->execute(
                $event->getFirmId(), $event->getProgramId(), $event->getParticipantId(), $event->getMessageForClient());
    }

}

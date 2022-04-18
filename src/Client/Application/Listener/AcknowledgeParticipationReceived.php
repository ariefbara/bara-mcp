<?php

namespace Client\Application\Listener;

use Client\Application\Service\ExecuteTask;
use Client\Domain\Task\AddClientParticipant;
use Config\EventList;
use Resources\Application\Event\Event;
use Resources\Application\Event\Listener;
use Resources\Domain\Event\CommonEvent;

class AcknowledgeParticipationReceived implements Listener
{

    /**
     * 
     * @var ExecuteTask
     */
    protected $clientExecuteTaskService;

    /**
     * 
     * @var AddClientParticipant
     */
    protected $addClientParticipantTask;

    /**
     * 
     * @var CommonEvent|null
     */
    protected $programApplicationReceivedEventListened;

    /**
     * 
     * @var CommonEvent|null
     */
    protected $programParticipationAcceptedEventListened;

    public function __construct(ExecuteTask $clientExecuteTaskService, AddClientParticipant $addClientParticipantTask)
    {
        $this->clientExecuteTaskService = $clientExecuteTaskService;
        $this->addClientParticipantTask = $addClientParticipantTask;
    }

    public function handle(Event $event): void
    {
        if ($event->getName() === EventList::PROGRAM_APPLICATION_RECEIVED) {
            $this->programApplicationReceivedEventListened = $event;
        } elseif ($event->getName() === EventList::PROGRAM_PARTICIPATION_ACCEPTED) {
            $this->programParticipationAcceptedEventListened = $event;
        }
        if ($this->programApplicationReceivedEventListened && $this->programParticipationAcceptedEventListened) {
            $this->execute();
        }
    }

    protected function execute(): void
    {
        $clientId = $this->programApplicationReceivedEventListened->getId();
        $participantId = $this->programParticipationAcceptedEventListened->getId();
        $this->clientExecuteTaskService->execute($clientId, $this->addClientParticipantTask, $participantId);
    }

}

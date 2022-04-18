<?php

namespace Client\Application\Listener;

use Client\Application\Service\ExecuteTask;
use Client\Domain\Task\AddClientRegistrant;
use Firm\Domain\Event\ProgramRegistrationReceived;
use Resources\Application\Event\Event;
use Resources\Application\Event\Listener;
use Resources\Domain\Event\CommonEvent;

class AcknowledgeRegistrationReceived implements Listener
{

    /**
     * 
     * @var ExecuteTask
     */
    protected $clientExecuteTaskService;

    /**
     * 
     * @var AddClientRegistrant
     */
    protected $addClientRegistrantTask;
    
    /**
     * 
     * @var CommonEvent|null
     */
    protected $programApplicationReceivedEventListened;
    
    /**
     * 
     * @var ProgramRegistrationReceived|null
     */
    protected $programRegistraitonReceivedEventListened;

    public function __construct(ExecuteTask $clientExecuteTaskService, AddClientRegistrant $addClientRegistrantTask)
    {
        $this->clientExecuteTaskService = $clientExecuteTaskService;
        $this->addClientRegistrantTask = $addClientRegistrantTask;
    }

    public function handle(Event $event): void
    {
        if ($event->getName() === \Config\EventList::PROGRAM_APPLICATION_RECEIVED) {
            $this->programApplicationReceivedEventListened = $event;
        } elseif ($event->getName() === \Config\EventList::PROGRAM_REGISTRATION_RECEIVED) {
            $this->programRegistraitonReceivedEventListened = $event;
        }
        if ($this->programApplicationReceivedEventListened && $this->programRegistraitonReceivedEventListened) {
            $this->execute();
        }
    }
    
    protected function execute(): void
    {
        $clientId = $this->programApplicationReceivedEventListened->getId();
        $registrantId = $this->programRegistraitonReceivedEventListened->getRegistrantId();
        $this->clientExecuteTaskService->execute($clientId, $this->addClientRegistrantTask, $registrantId);
    }

}

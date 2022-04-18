<?php

namespace Firm\Application\Listener;

use Client\Domain\Event\ClientHasAppliedToProgram;
use Firm\Application\Service\Firm\Program\ExecuteTask;
use Firm\Domain\Task\InProgram\ReceiveApplicationFromClient;
use Resources\Application\Event\Event;
use Resources\Application\Event\Listener;

class ListeningToProgramRegistrationFromClient implements Listener
{

    /**
     * 
     * @var ExecuteTask
     */
    protected $service;

    /**
     * 
     * @var ReceiveApplicationFromClient
     */
    protected $task;

    public function __construct(ExecuteTask $service, ReceiveApplicationFromClient $task)
    {
        $this->service = $service;
        $this->task = $task;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(ClientHasAppliedToProgram $event): void
    {
        $this->service->execute($event->getProgramId(), $this->task, $event->getClientId());
    }

}

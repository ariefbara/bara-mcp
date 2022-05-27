<?php

namespace Firm\Application\Listener;

use Client\Domain\Event\ClientHasAppliedToProgram;
use Firm\Application\Service\ExecuteResponsiveTask;
use Firm\Domain\Task\Responsive\ReceiveProgramApplicationFromClient;
use Firm\Domain\Task\Responsive\ReceiveProgramApplicationPayload;
use Resources\Application\Event\Event;
use Resources\Application\Event\Listener;

class ListeningEventsToReceiveProgramApplicationFromClient implements Listener
{

    /**
     * 
     * @var ExecuteResponsiveTask
     */
    protected $executeResposinveTaskService;

    /**
     * 
     * @var ReceiveProgramApplicationFromClient
     */
    protected $receiveProgramApplicationFromClientTask;

    public function __construct(
            ExecuteResponsiveTask $executeResposinveTaskService,
            ReceiveProgramApplicationFromClient $receiveProgramApplicationFromClientTask)
    {
        $this->executeResposinveTaskService = $executeResposinveTaskService;
        $this->receiveProgramApplicationFromClientTask = $receiveProgramApplicationFromClientTask;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(ClientHasAppliedToProgram $event): void
    {
        $payload = new ReceiveProgramApplicationPayload($event->getProgramId(), $event->getClientId());
        $this->executeResposinveTaskService->execute($this->receiveProgramApplicationFromClientTask, $payload);
    }

}

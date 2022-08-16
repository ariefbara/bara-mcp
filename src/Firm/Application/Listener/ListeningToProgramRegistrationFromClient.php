<?php

namespace Firm\Application\Listener;

use Client\Domain\Event\ClientHasAppliedToProgram;
use Firm\Application\Service\FirmRepository;
use Firm\Domain\Task\InFirm\AcceptProgramApplicationFromClient;
use Firm\Domain\Task\InFirm\AcceptProgramApplicationFromClientPayload;
use Resources\Application\Event\Event;
use Resources\Application\Event\Listener;

class ListeningToProgramRegistrationFromClient implements Listener
{

    /**
     * 
     * @var FirmRepository
     */
    protected $firmRepository;

    /**
     * 
     * @var AcceptProgramApplicationFromClient
     */
    protected $task;

    public function __construct(FirmRepository $firmRepository, AcceptProgramApplicationFromClient $task)
    {
        $this->firmRepository = $firmRepository;
        $this->task = $task;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(ClientHasAppliedToProgram $event): void
    {
        $firm = $this->firmRepository->ofId($event->getFirmId());
        $payload = new AcceptProgramApplicationFromClientPayload($event->getProgramId(), $event->getClientId());
        $this->task->execute($firm, $payload);
        $this->firmRepository->update();
    }

}

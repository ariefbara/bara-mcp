<?php

namespace Firm\Application\Listener;

use Client\Domain\Event\ClientHasAppliedToProgram;
use Resources\Application\Event\AdvanceDispatcher;
use Resources\Application\Event\Event;
use Resources\Application\Event\Listener;

class AcceptProgramApplicationFromClient implements Listener
{

    /**
     * 
     * @var ProgramRepository
     */
    protected $programRepository;

    /**
     * 
     * @var ClientRepository
     */
    protected $clientRepository;

    /**
     * 
     * @var AdvanceDispatcher
     */
    protected $dispatcher;

    public function __construct(
            ProgramRepository $programRepository, ClientRepository $clientRepository, AdvanceDispatcher $dispatcher)
    {
        $this->programRepository = $programRepository;
        $this->clientRepository = $clientRepository;
        $this->dispatcher = $dispatcher;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(ClientHasAppliedToProgram $event): void
    {
        $client = $this->clientRepository->ofId($event->getClientId());
        $program = $this->programRepository->aProgramOfId($event->getProgramId());
        $program->receiveApplication($client);
        $this->programRepository->update();
        
        $this->dispatcher->dispatch($program);
    }

}

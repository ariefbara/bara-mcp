<?php

namespace Firm\Domain\Task\InProgram;

use Config\EventList;
use Firm\Domain\Model\Firm\IProgramTask;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Task\Dependency\Firm\ClientRepository;
use Resources\Application\Event\AdvanceDispatcher;
use Resources\Domain\Event\CommonEvent;

class ReceiveApplicationFromClient implements IProgramTask
{

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

    public function __construct(ClientRepository $clientRepository, AdvanceDispatcher $dispatcher)
    {
        $this->clientRepository = $clientRepository;
        $this->dispatcher = $dispatcher;
    }

    /**
     * 
     * @param Program $program
     * @param string $payload clientId
     * @return void
     */
    public function execute(Program $program, $payload): void
    {
        $client = $this->clientRepository->ofId($payload);
        $program->receiveApplication($client);
        $this->dispatcher->dispatchEvent(new CommonEvent(EventList::PROGRAM_APPLICATION_RECEIVED, $payload));
        $this->dispatcher->dispatch($program);
    }

}

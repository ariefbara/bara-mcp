<?php

namespace Client\Domain\Task;

use Client\Domain\Model\Client;
use Client\Domain\Model\IClientTask;
use Client\Domain\Task\Repository\Firm\ProgramRepository;
use Resources\Application\Event\AdvanceDispatcher;

class ApplyProgram implements IClientTask
{

    /**
     * 
     * @var ProgramRepository
     */
    protected $programRepository;

    /**
     * 
     * @var AdvanceDispatcher
     */
    protected $dispatcher;

    public function __construct(ProgramRepository $programRepository, AdvanceDispatcher $dispatcher)
    {
        $this->programRepository = $programRepository;
        $this->dispatcher = $dispatcher;
    }

    /**
     * 
     * @param Client $client
     * @param string $payload programId
     * @return void
     */
    public function execute(Client $client, $payload): void
    {
        $program = $this->programRepository->ofId($payload);
        $client->applyToProgram($program);
        $this->dispatcher->dispatch($client);
    }

}

<?php

namespace Client\Domain\Task;

use Client\Domain\Model\Client;
use Client\Domain\Model\IClientTask;
use Resources\Application\Event\AdvanceDispatcher;

class ApplyProgram implements IClientTask
{

    /**
     * 
     * @var AdvanceDispatcher
     */
    protected $dispatcher;

    public function __construct(AdvanceDispatcher $dispatcher)
    {
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
        $client->applyToProgram($payload);
        $this->dispatcher->dispatch($client);
    }

}

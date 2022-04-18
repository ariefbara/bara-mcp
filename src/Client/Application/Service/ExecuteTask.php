<?php

namespace Client\Application\Service;

use Client\Domain\Model\IClientTask;

class ExecuteTask
{
    /**
     * 
     * @var ClientRepository
     */
    protected $clientRepository;
    
    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }
    
    public function execute(string $clientId, IClientTask $task, $payload): void
    {
        $this->clientRepository->aClientOfId($clientId)
                ->executeTask($task, $payload);
        $this->clientRepository->update();
    }

}

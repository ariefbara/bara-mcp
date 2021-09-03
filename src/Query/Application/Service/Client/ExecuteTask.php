<?php

namespace Query\Application\Service\Client;

use Query\Domain\Model\Firm\ITaskExecutableByClient;

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
    
    public function execute(string $firmId, string $clientId, ITaskExecutableByClient $task): void
    {
        $this->clientRepository->aClientInFirm($firmId, $clientId)
                ->executeTask($task);
    }

}

<?php

namespace Client\Application\Service\Client;

use Client\Application\Service\ClientRepository;

class RemoveCV
{

    /**
     * 
     * @var ClientRepository
     */
    protected $clientRepository;

    /**
     * 
     * @var ClientCVRepository
     */
    protected $clientCVRepository;

    public function __construct(ClientRepository $clientRepository, ClientCVRepository $clientCVRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->clientCVRepository = $clientCVRepository;
    }
    
    public function execute(string $firmId, string $clientId, string $clientCVFormId): void
    {
        $clientCV = $this->clientCVRepository->aClientCVCorrespondWithCVForm($clientId, $clientCVFormId);
        $this->clientRepository->ofId($firmId, $clientId)->removeCV($clientCV);
        $this->clientCVRepository->update();
    }

}

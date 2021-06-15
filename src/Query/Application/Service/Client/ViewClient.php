<?php

namespace Query\Application\Service\Client;

use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\ClientSearchRequest;

class ViewClient
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
    
    public function showById(string $firmId, string $clientId, string $id): Client
    {
        return $this->clientRepository->aClientInFirm($firmId, $clientId)
                ->viewClient($this->clientRepository, $id);
    }
    
    public function search(string $firmId, string $clientId, ClientSearchRequest $clientSearchRequest): array
    {
        return $this->clientRepository->aClientInFirm($firmId, $clientId)
                ->viewAllAccessibleClients($this->clientRepository, $clientSearchRequest);
    }

}

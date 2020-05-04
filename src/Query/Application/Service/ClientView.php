<?php

namespace Query\Application\Service;

use Query\Domain\Model\Client;

class ClientView
{
    /**
     *
     * @var ClientRepository
     */
    protected $clientRepository;
    
    function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }
    
    public function showById(string $clientId): Client
    {
        return $this->clientRepository->ofId($clientId);
    }
    
    /**
     * 
     * @param int $page
     * @param int $pageSize
     * @return Client[]
     */
    public function showAll(int $page, int $pageSize)
    {
        return $this->clientRepository->all($page, $pageSize);
    }

}

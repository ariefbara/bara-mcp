<?php

namespace Client\Application\Service;

use Client\Domain\Model\Client;

class ClientChangeProfile
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
    
    public function execute(string $clientId, string $name): void
    {
        $client = $this->clientRepository->ofId($clientId);
        $client->changeProfile($name);
        $this->clientRepository->update();
    }

}

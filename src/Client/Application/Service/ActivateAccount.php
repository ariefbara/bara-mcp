<?php

namespace Client\Application\Service;

class ActivateAccount
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
    
    public function execute(string $firmIdentifier, string $email, string $activationCode)
    {
        $client = $this->clientRepository->ofEmail($firmIdentifier, $email);
        $client->activate($activationCode);
        $this->clientRepository->update();
    }

}

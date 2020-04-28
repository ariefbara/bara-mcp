<?php

namespace Client\Application\Service;

class ClientActivate
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
    
    public function execute(string $email, string $activationCode): void
    {
        $this->clientRepository->ofEmail($email)
                ->activate($activationCode);
        $this->clientRepository->update();
    }

}

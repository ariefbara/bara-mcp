<?php

namespace Client\Application\Service;

class ClientChangePassword
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
    
    public function execute(string $clientId, string $previousPassword, string $newPassword): void
    {
        $this->clientRepository->ofId($clientId)
                ->changePassword($previousPassword, $newPassword);
        $this->clientRepository->update();
    }

}

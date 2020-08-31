<?php

namespace Client\Application\Service;

class ChangePassword
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
    
    public function execute(string $firmId, string $clientId, string $previousPassword, string $newPassword): void
    {
        $this->clientRepository->ofId($firmId, $clientId)->changePassword($previousPassword, $newPassword);
        $this->clientRepository->update();
    }

}

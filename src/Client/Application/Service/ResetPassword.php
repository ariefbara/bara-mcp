<?php

namespace Client\Application\Service;

class ResetPassword
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
    
    public function execute(string $firmIdentifier, string $email, string $resetPasswordCode, string $password): void
    {
        $this->clientRepository->ofEmail($firmIdentifier, $email)->resetPassword($resetPasswordCode, $password);
        $this->clientRepository->update();
    }

}

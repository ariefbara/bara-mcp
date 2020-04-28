<?php

namespace Client\Application\Service;

class ClientResetPassword
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
    
    public function execute(string $email, string $resetPasswordCode, string $password): void
    {
        $this->clientRepository->ofEmail($email)
                ->resetPassword($resetPasswordCode, $password);
        $this->clientRepository->update();
    }

}

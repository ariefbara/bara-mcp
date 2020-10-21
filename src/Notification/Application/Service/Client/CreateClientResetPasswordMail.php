<?php

namespace Notification\Application\Service\Client;

class CreateClientResetPasswordMail
{

    /**
     *
     * @var ClientMailRepository
     */
    protected $clientMailRepository;

    /**
     *
     * @var ClientRepository
     */
    protected $clientRepository;
    
    public function __construct(ClientMailRepository $clientMailRepository, ClientRepository $clientRepository)
    {
        $this->clientMailRepository = $clientMailRepository;
        $this->clientRepository = $clientRepository;
    }
    
    public function execute(string $clientId): void
    {
        $id = $this->clientMailRepository->nextIdentity();
        $clientMail = $this->clientRepository->ofId($clientId)->createResetPasswordMail($id);
        $this->clientMailRepository->add($clientMail);
    }


}

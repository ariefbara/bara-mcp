<?php

namespace Client\Application\Service;

use Resources\Application\Event\Dispatcher;

class ClientGenerateResetPasswordCode
{
    /**
     *
     * @var ClientRepository
     */
    protected $clientRepository;
    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;
    
    function __construct(ClientRepository $clientRepository, Dispatcher $dispatcher)
    {
        $this->clientRepository = $clientRepository;
        $this->dispatcher = $dispatcher;
    }

    
    public function execute(string $email): void
    {
        $client = $this->clientRepository->ofEmail($email);
        $client->generateResetPasswordCode();
        $this->clientRepository->update();
        $this->dispatcher->dispatch($client);
    }

}

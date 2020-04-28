<?php

namespace Client\Application\Service;

use Client\Domain\Model\Client;
use Resources\ {
    Application\Event\Dispatcher,
    Exception\RegularException
};

class ClientSignup
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

    public function __construct(ClientRepository $clientRepository, Dispatcher $dispatcher)
    {
        $this->clientRepository = $clientRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(string $name, string $email, string $password): Client
    {
        $this->assertEmailAvailable($email);

        $id = $this->clientRepository->nextIdentity();
        $client = new Client($id, $name, $email, $password);
        $this->clientRepository->add($client);
        $this->dispatcher->dispatch($client);
        return $client;
    }

    private function assertEmailAvailable(string $email): void
    {
        if ($this->clientRepository->containRecordWithEmail($email)) {
            $errorDetail = "conflict: email already registered";
            throw RegularException::conflict($errorDetail);
        }
    }

}

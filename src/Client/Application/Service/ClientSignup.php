<?php

namespace Client\Application\Service;

use Client\Domain\Model\ {
    Client,
    ClientData
};
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
     * @var FirmRepository
     */
    protected $firmRepository;

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(ClientRepository $clientRepository, FirmRepository $firmRepository, Dispatcher $dispatcher)
    {
        $this->clientRepository = $clientRepository;
        $this->firmRepository = $firmRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(string $firmIdentifier, ClientData $clientData)
    {
        $this->assertEmailAvailable($firmIdentifier, $clientData->getEmail());
        
        $id = $this->clientRepository->nextIdentity();
        $firm = $this->firmRepository->ofIdentifier($firmIdentifier);
        $client = new Client($firm, $id, $clientData);
        $this->clientRepository->add($client);
        
        $this->dispatcher->dispatch($client);
    }
    
    protected function assertEmailAvailable(string $firmIdentifier, string $email): void
    {
        if ($this->clientRepository->containRecordWithEmail($firmIdentifier, $email)) {
            $errorDetail = 'conflict: email already registered';
            throw RegularException::conflict($errorDetail);
        }
    }

}

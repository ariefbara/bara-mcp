<?php

namespace Firm\Application\Service\Firm;

use Firm\ {
    Application\Service\FirmRepository,
    Domain\Model\Firm\ClientData
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
    protected $dipatcher;

    function __construct(ClientRepository $clientRepository, FirmRepository $firmRepository, Dispatcher $dipatcher)
    {
        $this->clientRepository = $clientRepository;
        $this->firmRepository = $firmRepository;
        $this->dipatcher = $dipatcher;
    }

    public function execute(string $firmIdentifier, ClientData $clientData)
    {
        $this->assertEmailAvailable($firmIdentifier, $clientData->getEmail());
        
        $id = $this->clientRepository->nextIdentity();
        $firm = $this->firmRepository->ofIdentifier($firmIdentifier);
        $client = $firm->acceptClientSignup($id, $clientData);
        $this->clientRepository->add($client);
        
        $this->dipatcher->dispatch($firm);
    }
    
    protected function assertEmailAvailable(string $firmIdentifier, string $email): void
    {
        if ($this->clientRepository->containRecordWithEmail($firmIdentifier, $email)) {
            $errorDetail = 'conflict: email already registered';
            throw RegularException::conflict($errorDetail);
        }
    }

}

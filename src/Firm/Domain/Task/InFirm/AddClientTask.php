<?php

namespace Firm\Domain\Task\InFirm;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\ClientRegistrationData;
use Firm\Domain\Model\Firm\FirmTaskExecutableByManager;
use Firm\Domain\Task\Dependency\Firm\ClientRepository;

class AddClientTask implements FirmTaskExecutableByManager
{

    /**
     * 
     * @var ClientRepository
     */
    protected $clientRepository;

    /**
     * 
     * @var ClientRegistrationData
     */
    protected $payload;

    /**
     * 
     * @var string|null
     */
    public $addedClientId;

    public function __construct(ClientRepository $clientRepository, ClientRegistrationData $payload)
    {
        $this->clientRepository = $clientRepository;
        $this->payload = $payload;
    }

    public function executeInFirm(Firm $firm): void
    {
        $this->addedClientId = $this->clientRepository->nextIdentity();
        $client = $firm->createClient($this->addedClientId, $this->payload);
        $this->clientRepository->add($client);
    }

}

<?php

namespace Query\Domain\Task\InFirm;

use Query\Domain\Model\Firm;
use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\ITaskInFirmExecutableByManager;
use Query\Domain\Task\Dependency\Firm\ClientFilter;
use Query\Domain\Task\Dependency\Firm\ClientRepository;

class ShowAllClientTask implements ITaskInFirmExecutableByManager
{

    /**
     * 
     * @var ClientRepository
     */
    protected $clientRepository;

    /**
     * 
     * @var ClientFilter
     */
    protected $payload;

    /**
     * 
     * @var Client[]
     */
    public $results;

    public function __construct(ClientRepository $clientRepository, ClientFilter $payload)
    {
        $this->clientRepository = $clientRepository;
        $this->payload = $payload;
    }

    public function executeTaskInFirm(Firm $firm): void
    {
        $this->results = $this->clientRepository->allClientsInFirm($firm->getId(), $this->payload);
    }

}

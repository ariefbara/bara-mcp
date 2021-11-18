<?php

namespace Query\Domain\Task\InFirm;

use Query\Domain\Model\Firm;
use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\ITaskInFirmExecutableByManager;
use Query\Domain\Task\Dependency\Firm\ClientRepository;

class ShowClientTask implements ITaskInFirmExecutableByManager
{

    /**
     * 
     * @var ClientRepository
     */
    protected $clientRepository;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var Client|null
     */
    public $result;

    public function __construct(ClientRepository $clientRepository, string $id)
    {
        $this->clientRepository = $clientRepository;
        $this->id = $id;
    }

    public function executeTaskInFirm(Firm $firm): void
    {
        $this->result = $this->clientRepository->aClientInFirm($firm->getId(), $this->id);
    }

}

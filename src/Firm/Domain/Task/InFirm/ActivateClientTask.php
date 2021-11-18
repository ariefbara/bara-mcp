<?php

namespace Firm\Domain\Task\InFirm;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\FirmTaskExecutableByManager;
use Firm\Domain\Task\Dependency\Firm\ClientRepository;

class ActivateClientTask implements FirmTaskExecutableByManager
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

    public function __construct(ClientRepository $clientRepository, string $id)
    {
        $this->clientRepository = $clientRepository;
        $this->id = $id;
    }

    public function executeInFirm(Firm $firm): void
    {
        $client = $this->clientRepository->ofId($this->id);
        $client->assertManageableInFirm($firm);
        $client->activate();
    }

}

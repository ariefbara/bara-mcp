<?php

namespace Client\Domain\Task;

use Client\Domain\Model\Client;
use Client\Domain\Model\IClientTask;
use Client\Domain\Task\Repository\Firm\Client\ClientRegistrantRepository;

class CancelRegistration implements IClientTask
{

    /**
     * 
     * @var ClientRegistrantRepository
     */
    protected $clientRegistrantRepository;

    public function __construct(ClientRegistrantRepository $clientRegistrantRepository)
    {
        $this->clientRegistrantRepository = $clientRegistrantRepository;
    }

    public function execute(Client $client, $payload): void
    {
        $clientRegistrant = $this->clientRegistrantRepository->ofId($payload);
        $clientRegistrant->assertManageableByClient($client);
        $clientRegistrant->cancel();
    }

}

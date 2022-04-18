<?php

namespace Client\Domain\Task;

use Client\Domain\Model\Client;
use Client\Domain\Model\IClientTask;
use Client\Domain\Task\Repository\Firm\Client\ClientRegistrantRepository;
use Client\Domain\Task\Repository\Firm\Program\RegistrantRepository;

class AddClientRegistrant implements IClientTask
{

    /**
     * 
     * @var ClientRegistrantRepository
     */
    protected $clientRegistrantRepository;

    /**
     * 
     * @var RegistrantRepository
     */
    protected $registrantRepository;
    public $addedClientRegistrantId;

    public function __construct(
            ClientRegistrantRepository $clientRegistrantRepository, RegistrantRepository $registrantRepository)
    {
        $this->clientRegistrantRepository = $clientRegistrantRepository;
        $this->registrantRepository = $registrantRepository;
    }

    /**
     * 
     * @param Client $client
     * @param string $payload registrantId
     * @return void
     */
    public function execute(Client $client, $payload): void
    {
        $registrant = $this->registrantRepository->ofId($payload);
        $clientRegistrant = $client->createClientRegistrant($payload, $registrant);
        $this->clientRegistrantRepository->add($clientRegistrant);
        $this->addedClientRegistrantId = $payload;
    }

}

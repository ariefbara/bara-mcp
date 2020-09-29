<?php

namespace Query\Domain\Service\Firm;

use Query\Domain\Model\Firm\Client;

class ClientFinder
{

    /**
     *
     * @var ClientRepository
     */
    protected $clientRepository;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function findByEmail(string $firmId, string $clientEmail): Client
    {
        return $this->clientRepository->aClientHavingEmail($firmId, $clientEmail);
    }

}

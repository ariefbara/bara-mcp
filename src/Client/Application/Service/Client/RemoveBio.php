<?php

namespace Client\Application\Service\Client;

use Client\Application\Service\ClientRepository;

class RemoveBio
{

    /**
     * 
     * @var ClientRepository
     */
    protected $clientRepository;

    /**
     * 
     * @var ClientBioRepository
     */
    protected $clientBioRepository;

    public function __construct(ClientRepository $clientRepository, ClientBioRepository $clientBioRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->clientBioRepository = $clientBioRepository;
    }
    
    public function execute(string $firmId, string $clientId, string $bioFormId): void
    {
        $clientBio = $this->clientBioRepository->aBioCorrespondWithForm($clientId, $bioFormId);
        $this->clientRepository->ofId($firmId, $clientId)->removeBio($clientBio);
        $this->clientBioRepository->update();
    }

}

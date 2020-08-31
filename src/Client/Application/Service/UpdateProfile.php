<?php

namespace Client\Application\Service;

class UpdateProfile
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
    
    public function execute(string $firmId, string $clientId, ?string $firstName, ?string $lastName): void
    {
        $this->clientRepository->ofId($firmId, $clientId)->updateProfile($firstName, $lastName);
        $this->clientRepository->update();
    }

}

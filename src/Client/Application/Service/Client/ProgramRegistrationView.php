<?php

namespace Client\Application\Service\Client;

use Client\Domain\Model\Client\ProgramRegistration;

class ProgramRegistrationView
{
    /**
     *
     * @var ProgramRegistrationRepository
     */
    protected $programRegistrationRepository;
    
    function __construct(ProgramRegistrationRepository $programRegistrationRepository)
    {
        $this->programRegistrationRepository = $programRegistrationRepository;
    }
    
    public function showById(string $clientId, string $programRegistrationId): ProgramRegistration
    {
        return $this->programRegistrationRepository->ofId($clientId, $programRegistrationId);
    }
    
    /**
     * 
     * @param string $clientId
     * @param int $page
     * @param int $pageSize
     * @return ProgramRegistration[]
     */
    public function showAll(string $clientId, int $page, int $pageSize)
    {
        return $this->programRegistrationRepository->all($clientId, $page, $pageSize);
    }

}

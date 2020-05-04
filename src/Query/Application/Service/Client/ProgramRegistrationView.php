<?php

namespace Query\Application\Service\Client;

use Query\Domain\Model\Firm\Program\Registrant;

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

    /**
     * 
     * @param string $clientId
     * @param int $page
     * @param int $pageSize
     * @return Registrant[]
     */
    public function showAll(string $clientId, int $page, int $pageSize)
    {
        return $this->programRegistrationRepository->allProgramRegistrationsOfClient($clientId, $page, $pageSize);
    }

    public function showById(string $clientId, string $programRegistrationId): Registrant
    {
        return $this->programRegistrationRepository->aProgramRegistrationOfClient($clientId, $programRegistrationId);
    }

}

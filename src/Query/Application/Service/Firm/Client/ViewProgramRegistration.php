<?php

namespace Query\Application\Service\Firm\Client;

use Query\Domain\Model\Firm\Client\ClientRegistrant;


class ViewProgramRegistration
{

    /**
     *
     * @var ProgramRegistrationRepository
     */
    protected $programRegistrationRepository;

    public function __construct(ProgramRegistrationRepository $programRegistrationRepository)
    {
        $this->programRegistrationRepository = $programRegistrationRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param int $page
     * @param int $pageSize
     * @return ClientRegistrant[]
     */
    public function showAll(string $firmId, string $clientId, int $page, int $pageSize)
    {
        return $this->programRegistrationRepository->all($firmId, $clientId, $page, $pageSize);
    }

    public function showById(string $firmId, string $clientId, string $programRegistrationId): ClientRegistrant
    {
        return $this->programRegistrationRepository->ofId($firmId, $clientId, $programRegistrationId);
    }

}

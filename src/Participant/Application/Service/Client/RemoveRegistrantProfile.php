<?php

namespace Participant\Application\Service\Client;

use Participant\Application\Service\RegistrantProfileRepository;

class RemoveRegistrantProfile
{

    /**
     * 
     * @var ClientRegistrantRepository
     */
    protected $clientRegistrantRepository;

    /**
     * 
     * @var RegistrantProfileRepository
     */
    protected $registrantProfileRepository;

    function __construct(ClientRegistrantRepository $clientRegistrantRepository,
            RegistrantProfileRepository $registrantProfileRepository)
    {
        $this->clientRegistrantRepository = $clientRegistrantRepository;
        $this->registrantProfileRepository = $registrantProfileRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $programRegistrationId, string $programsProfileFormId): void
    {
        $registrantProfile = $this->registrantProfileRepository
                ->aRegistrantProfileCorrespondWithProgramsProfileForm($programRegistrationId, $programsProfileFormId);
        
        $this->clientRegistrantRepository->aClientRegistrant($firmId, $clientId, $programRegistrationId)
                ->removeProfile($registrantProfile);
        
        $this->registrantProfileRepository->update();
    }

}

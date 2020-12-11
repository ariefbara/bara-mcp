<?php

namespace Participant\Application\Service\User;

use Participant\Application\Service\RegistrantProfileRepository;

class RemoveRegistrantProfile
{

    /**
     * 
     * @var RegistrantProfileRepository
     */
    protected $registrantProfileRepository;

    /**
     * 
     * @var UserRegistrantRepository
     */
    protected $userRegistrantRepository;

    function __construct(
            RegistrantProfileRepository $registrantProfileRepository, UserRegistrantRepository $userRegistrantRepository)
    {
        $this->registrantProfileRepository = $registrantProfileRepository;
        $this->userRegistrantRepository = $userRegistrantRepository;
    }

    public function execute(string $userId, string $programRegistrationId, string $programsProfileFormId): void
    {
        $registrantProfile = $this->registrantProfileRepository
                ->aRegistrantProfileCorrespondWithProgramsProfileForm($programRegistrationId, $programsProfileFormId);
        
        $this->userRegistrantRepository->aUserRegistrant($userId, $programRegistrationId)
                ->removeProfile($registrantProfile);
        
        $this->registrantProfileRepository->update();
    }

}

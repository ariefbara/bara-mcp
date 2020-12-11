<?php

namespace Participant\Application\Service\User;

use Participant\Application\Service\ProgramsProfileFormRepository;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class SubmitRegistrantProfile
{

    /**
     * 
     * @var UserRegistrantRepository
     */
    protected $userRegistrantRepository;

    /**
     * 
     * @var ProgramsProfileFormRepository
     */
    protected $programsProfileFormRepository;

    function __construct(UserRegistrantRepository $userRegistrantRepository,
            ProgramsProfileFormRepository $programsProfileFormRepository)
    {
        $this->userRegistrantRepository = $userRegistrantRepository;
        $this->programsProfileFormRepository = $programsProfileFormRepository;
    }

    public function execute(
            string $userId, string $programRegistrationId, string $programsProfileFormId, FormRecordData $formRecordData): void
    {
        $programsProfileForm = $this->programsProfileFormRepository->ofId($programsProfileFormId);
        $this->userRegistrantRepository->aUserRegistrant($userId, $programRegistrationId)
                ->submitProfile($programsProfileForm, $formRecordData);
        
        $this->userRegistrantRepository->update();
    }

}

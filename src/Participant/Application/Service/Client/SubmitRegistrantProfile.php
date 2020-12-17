<?php

namespace Participant\Application\Service\Client;

use Participant\Application\Service\ProgramsProfileFormRepository;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class SubmitRegistrantProfile
{

    /**
     * 
     * @var ClientRegistrantRepository
     */
    protected $clientRegistrantRepository;

    /**
     * 
     * @var ProgramsProfileFormRepository
     */
    protected $programsProfileFormRepository;

    function __construct(
            ClientRegistrantRepository $clientRegistrantRepository,
            ProgramsProfileFormRepository $programsProfileFormRepository)
    {
        $this->clientRegistrantRepository = $clientRegistrantRepository;
        $this->programsProfileFormRepository = $programsProfileFormRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $programRegistrationId, string $programsProfileFormId,
            FormRecordData $formRecordData): void
    {
        $programsProfileForm = $this->programsProfileFormRepository->ofId($programsProfileFormId);
        $this->clientRegistrantRepository->aClientRegistrant($firmId, $clientId, $programRegistrationId)
                ->submitProfile($programsProfileForm, $formRecordData);
        
        $this->clientRegistrantRepository->update();
    }

}

<?php

namespace Participant\Application\Service\Client;

use Participant\Application\Service\ProgramsProfileFormRepository;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class SubmitParticipantProfile
{

    /**
     * 
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;

    /**
     * 
     * @var ProgramsProfileFormRepository
     */
    protected $programsProfileFormRepository;

    function __construct(ClientParticipantRepository $clientParticipantRepository,
            ProgramsProfileFormRepository $programsProfileFormRepository)
    {
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->programsProfileFormRepository = $programsProfileFormRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $programParticipationId, string $programsProfileFormId,
            FormRecordData $formRecordData): void
    {
        $programsProfileForm = $this->programsProfileFormRepository->ofId($programsProfileFormId);
        $this->clientParticipantRepository->aClientParticipant($firmId, $clientId, $programParticipationId)
                ->submitProfile($programsProfileForm, $formRecordData);
        $this->clientParticipantRepository->update();
    }

}

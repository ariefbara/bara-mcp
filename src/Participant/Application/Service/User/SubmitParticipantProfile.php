<?php

namespace Participant\Application\Service\User;

use Participant\Application\Service\ProgramsProfileFormRepository;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class SubmitParticipantProfile
{

    /**
     * 
     * @var UserParticipantRepository
     */
    protected $userParticipantRepository;

    /**
     * 
     * @var ProgramsProfileFormRepository
     */
    protected $programsProfileFormRepository;

    function __construct(UserParticipantRepository $userParticipantRepository,
            ProgramsProfileFormRepository $programsProfileFormRepository)
    {
        $this->userParticipantRepository = $userParticipantRepository;
        $this->programsProfileFormRepository = $programsProfileFormRepository;
    }

    public function execute(
            string $userId, string $programParticipationId, string $programsProfileFormId,
            FormRecordData $formRecordData): void
    {
        $programProfileForm = $this->programsProfileFormRepository->ofId($programsProfileFormId);
        $this->userParticipantRepository->aUserParticipant($userId, $programParticipationId)
                ->submitProfile($programProfileForm, $formRecordData);
        $this->userParticipantRepository->update();
    }

}

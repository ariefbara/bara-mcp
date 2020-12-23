<?php

namespace Participant\Application\Service\User;

use Participant\Application\Service\ParticipantProfileRepository;

class RemoveParticipantProfile
{

    /**
     * 
     * @var ParticipantProfileRepository
     */
    protected $participantProfileRepository;

    /**
     * 
     * @var UserParticipantRepository
     */
    protected $userParticipantRepository;

    function __construct(
            ParticipantProfileRepository $participantProfileRepository,
            UserParticipantRepository $userParticipantRepository)
    {
        $this->participantProfileRepository = $participantProfileRepository;
        $this->userParticipantRepository = $userParticipantRepository;
    }

    public function execute(string $userId, string $programParticipationId, string $programsProfileFormId): void
    {
        $participantProfile = $this->participantProfileRepository
                ->aParticipantProfileCorrespondWithProgramsProfileForm($programParticipationId, $programsProfileFormId);
        $this->userParticipantRepository->aUserParticipant($userId, $programParticipationId)
                ->removeProfile($participantProfile);
        $this->participantProfileRepository->update();
    }

}

<?php

namespace Participant\Application\Service\Client;

use Participant\Application\Service\ParticipantProfileRepository;

class RemoveParticipantProfile
{

    /**
     * 
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;

    /**
     * 
     * @var ParticipantProfileRepository
     */
    protected $participantProfileRepository;

    function __construct(ClientParticipantRepository $clientParticipantRepository,
            ParticipantProfileRepository $participantProfileRepository)
    {
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->participantProfileRepository = $participantProfileRepository;
    }

    public function execute(string $firmId, string $clientId, string $programParticipationId,
            string $programsProfileFormId): void
    {
        $participantProfile = $this->participantProfileRepository
                ->aParticipantProfileCorrespondWithProgramsProfileForm($programParticipationId, $programsProfileFormId);
        $this->clientParticipantRepository->aClientParticipant($firmId, $clientId, $programParticipationId)
                ->removeProfile($participantProfile);
        $this->participantProfileRepository->update();
    }

}

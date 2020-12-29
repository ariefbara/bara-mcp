<?php

namespace Query\Application\Service\Firm\Client;

use Query\Domain\Model\Firm\Program\Participant\ParticipantProfile;

class ViewParticipantProfile
{

    /**
     * 
     * @var ParticipantProfileRepository
     */
    protected $participantProfileRepository;

    function __construct(ParticipantProfileRepository $participantProfileRepository)
    {
        $this->participantProfileRepository = $participantProfileRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param string $programParticipationId
     * @param int $page
     * @param int $pageSize
     * @return ParticipantProfile[]
     */
    public function showAll(string $firmId, string $clientId, string $programParticipationId, int $page, int $pageSize)
    {
        return $this->participantProfileRepository->allParticipantProfilesBelongsToParticipant(
                        $firmId, $clientId, $programParticipationId, $page, $pageSize);
    }

    public function showByProgramsProfileFormId(
            string $firmId, string $clientId, string $programParticipationId, string $programsProfileFormId): ParticipantProfile
    {
        return $this->participantProfileRepository->aParticipantProfileBelongsToClientCorrespondWithProgramsProfileForm(
                        $firmId, $clientId, $programParticipationId, $programsProfileFormId);
    }

}

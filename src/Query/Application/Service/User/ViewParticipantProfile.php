<?php

namespace Query\Application\Service\User;

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
     * @param string $userId
     * @param string $programParticipationId
     * @param int $page
     * @param int $pageSize
     * @return ParticipantProfile[]
     */
    public function showAll(string $userId, string $programParticipationId, int $page, int $pageSize)
    {
        return $this->participantProfileRepository->allParticipantProfilesBelongsToUser(
                        $userId, $programParticipationId, $page, $pageSize);
    }

    public function showByProgramsProfileFormId(string $userId, string $programParticipationId, string $programsProfileFormId): ParticipantProfile
    {
        return $this->participantProfileRepository->aParticipantProfileBelongsToUserCorrespondWithProgramsProfileForm(
                        $userId, $programParticipationId, $programsProfileFormId);
    }

}

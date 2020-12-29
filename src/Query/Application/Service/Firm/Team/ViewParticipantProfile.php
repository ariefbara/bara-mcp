<?php

namespace Query\Application\Service\Firm\Team;

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
     * @param string $teamId
     * @param string $programParticipationId
     * @param int $page
     * @param int $pageSize
     * @return ParticipantProfile[]
     */
    public function showAll(string $firmId, string $teamId, string $programParticipationId, int $page, int $pageSize)
    {
        return $this->participantProfileRepository->allParticipantProfilesBelongsToTeamCorrespondWithProgramsProfileForm(
                        $firmId, $teamId, $programParticipationId, $page, $pageSize);
    }

    public function showByProgramsProfileFormId(
            string $firmId, string $teamId, string $programParticipationId, string $programsProfileFormId): ParticipantProfile
    {
        return $this->participantProfileRepository->aParticipantProfileBelongsToTeamCorrespondWithProgramsProfileForm(
                        $firmId, $teamId, $programParticipationId, $programsProfileFormId);
    }

}

<?php

namespace Query\Application\Service\Firm\Team\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Participant\ParticipantActivity;

class ViewParticipantActivity
{

    /**
     *
     * @var ParticipantActivityRepository
     */
    protected $participantActivityRepository;

    function __construct(ParticipantActivityRepository $participantActivityRepository)
    {
        $this->participantActivityRepository = $participantActivityRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $teamId
     * @param string $programParticipationId
     * @param int $page
     * @param int $pageSize
     * @return ParticipantActivity[]
     */
    public function showAll(string $firmId, string $teamId, string $programParticipationId, int $page, int $pageSize)
    {
        return $this->participantActivityRepository->allActivitiesInTeamProgramParticipation(
                        $firmId, $teamId, $programParticipationId, $page, $pageSize);
    }

    public function showById(string $firmId, string $teamId, string $activityId): ParticipantActivity
    {
        return $this->participantActivityRepository->anActivityBelongsToTeam($firmId, $teamId, $activityId);
    }

}

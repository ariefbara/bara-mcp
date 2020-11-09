<?php

namespace Query\Application\Service\User\ProgramParticipation;

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
     * @param string $userId
     * @param string $programParticipationId
     * @param int $page
     * @param int $pageSize
     * @return ParticipantActivity[]
     */
    public function showAll(string $userId, string $programParticipationId, int $page, int $pageSize)
    {
        return $this->participantActivityRepository->allActivitiesInUserProgramParticipation(
                        $userId, $programParticipationId, $page, $pageSize);
    }

    public function showById(string $userId, string $activityId): ParticipantActivity
    {
        return $this->participantActivityRepository->anActivityBelongsToUser($userId, $activityId);
    }

}

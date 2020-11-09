<?php

namespace Query\Application\Service\Firm\Client\ProgramParticipation;

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
     * @param string $clientId
     * @param string $programParticipationId
     * @param int $page
     * @param int $pageSize
     * @return ParticipantActivity[]
     */
    public function showAll(string $firmId, string $clientId, string $programParticipationId, int $page, int $pageSize)
    {
        return $this->participantActivityRepository->allActivitiesInClientProgramParticipation(
                        $firmId, $clientId, $programParticipationId, $page, $pageSize);
    }

    public function showById(string $firmId, string $clientId, string $activityId): ParticipantActivity
    {
        return $this->participantActivityRepository->anActivityBelongsToClient($firmId, $clientId, $activityId);
    }

}

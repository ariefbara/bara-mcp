<?php

namespace Query\Application\Service\User\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Participant\ParticipantActivity;

interface ParticipantActivityRepository
{

    public function anActivityBelongsToUser(string $userId, string $activityId): ParticipantActivity;

    public function allActivitiesInUserProgramParticipation(
            string $userId, string $programParticipationId, int $page, int $pageSize);
}

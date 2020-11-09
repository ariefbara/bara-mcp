<?php

namespace Query\Application\Service\Firm\Client\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Participant\ParticipantActivity;

interface ParticipantActivityRepository
{

    public function anActivityBelongsToClient(string $firmId, string $clientId, string $activityId): ParticipantActivity;

    public function allActivitiesInClientProgramParticipation(
            string $firmId, string $clientId, string $programParticipationId, int $page, int $pageSize);
}

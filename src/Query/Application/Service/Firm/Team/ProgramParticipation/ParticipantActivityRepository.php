<?php

namespace Query\Application\Service\Firm\Team\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Participant\ParticipantActivity;

interface ParticipantActivityRepository
{

    public function anActivityBelongsToTeam(string $firmId, string $teamId, string $activityId): ParticipantActivity;

    public function allActivitiesInTeamProgramParticipation(
            string $firmId, string $teamId, string $programParticipationId, int $page, int $pageSize);
}

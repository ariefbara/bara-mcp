<?php

namespace Query\Application\Service\Firm\Team;

use Query\Domain\Model\Firm\Program\Participant\ParticipantProfile;

interface ParticipantProfileRepository
{
    public function aParticipantProfileBelongsToTeamCorrespondWithProgramsProfileForm(
            string $firmId, string $teamId, string $programParticipationId, string $programsProfileFormId): ParticipantProfile;

    public function allParticipantProfilesBelongsToTeamCorrespondWithProgramsProfileForm(
            string $firmId, string $teamId, string $programParticipationId, int $page, int $pageSize);
}

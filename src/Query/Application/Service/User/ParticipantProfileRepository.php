<?php

namespace Query\Application\Service\User;

use Query\Domain\Model\Firm\Program\Participant\ParticipantProfile;

interface ParticipantProfileRepository
{

    public function aParticipantProfileBelongsToUserCorrespondWithProgramsProfileForm(
            string $userId, string $programParticipationId, string $programsProfileFormId): ParticipantProfile;

    public function allParticipantProfilesBelongsToUser(
            string $userId, string $programParticipationId, int $page, int $pageSize);
}

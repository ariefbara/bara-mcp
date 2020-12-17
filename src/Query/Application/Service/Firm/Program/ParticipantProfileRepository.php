<?php

namespace Query\Application\Service\Firm\Program;

use Query\Domain\Model\Firm\Program\Participant\ParticipantProfile;

interface ParticipantProfileRepository
{

    public function aParticipantProfileInProgram(string $firmId, string $programId, string $participantProfileId): ParticipantProfile;

    public function allProfilesBelongsToParticipantInProgram(
            string $firmId, string $programId, string $participantId, int $page, int $pageSize);
}

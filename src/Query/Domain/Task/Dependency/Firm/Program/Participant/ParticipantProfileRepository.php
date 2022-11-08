<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\Participant\ParticipantProfile;

interface ParticipantProfileRepository
{

    public function allParticipantProfilesBelongsInProgram(string $programId, ParticipantProfileFilter $filter);

    public function aParticipantProfileBelongsInProgram(string $programId, string $participantProfileId): ParticipantProfile;
}

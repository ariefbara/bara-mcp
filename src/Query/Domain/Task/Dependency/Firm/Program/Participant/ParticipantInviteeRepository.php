<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\Participant\ParticipantInvitee;
use Query\Domain\Task\Dependency\ActivityInvitationFilter;

interface ParticipantInviteeRepository
{

    public function allInvitationsToParticipantInProgram(
            string $programId, string $participantId, ActivityInvitationFilter $filter);

    public function aParticipantInvitationInProgram(string $programId, string $id): ParticipantInvitee;
}

<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\Participant\ParticipantNote;

interface ParticipantNoteRepository
{

    public function aParticipantNoteBelongsToParticipant(string $participantId, string $id): ParticipantNote;
}

<?php

namespace Participant\Domain\Task\Dependency\Firm\Program\Participant;

use Participant\Domain\Model\Participant\ParticipantNote;

interface ParticipantNoteRepository
{

    public function nextIdentity(): string;

    public function add(ParticipantNote $participantNote): void;

    public function ofId(string $id): ParticipantNote;
}

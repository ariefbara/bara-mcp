<?php

namespace Query\Domain\Task\Dependency;

interface NoteRepository
{

    public function allNoteAccessibleByParticipant(string $participantId, NoteFilter $filter);
}

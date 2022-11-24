<?php

namespace Query\Domain\Task\Dependency;

interface NoteRepository
{

    public function allNoteAccessibleByParticipant(string $participantId, NoteFilter $filter);

    public function allNotesInProgramsMentoredByPersonnel(string $personnelId, NoteFilterForConsultant $filter);

    public function allNotesInProgramsCoordinatedByPersonnel(string $personnelId, NoteFilterForCoordinator $filter);
}

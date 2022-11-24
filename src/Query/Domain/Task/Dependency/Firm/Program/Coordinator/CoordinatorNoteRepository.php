<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Coordinator;

use Query\Domain\Model\Firm\Program\Coordinator\CoordinatorNote;

interface CoordinatorNoteRepository
{

    public function allCoordinatorNotesBelongsToPersonnel(string $personnelId, CoordinatorNoteFilter $filter);

    public function aCoordinatorNoteBelongsToPersonnel(string $personnelId, string $id): CoordinatorNote;
    
    public function aCoordinatorNoteAccessibleByParticipant(string $participantId, string $id): CoordinatorNote;
    
    public function aCoordinatorNoteInProgram(string $programId, string $id): CoordinatorNote;
}

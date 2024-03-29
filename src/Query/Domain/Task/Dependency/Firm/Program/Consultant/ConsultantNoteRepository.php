<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Consultant;

use Query\Domain\Model\Firm\Personnel\Consultant\ConsultantNote;

interface ConsultantNoteRepository
{

    public function allConsultantNotesBelongsToPersonnel(string $personnelId, ConsultantNoteFilter $filter);

    public function aConsultantNoteBelongsToPersonnel(string $personnelId, string $id): ConsultantNote;

    public function aConsultantNoteForAccessibleByParticipant(string $participantId, string $id): ConsultantNote;
    
    public function aConsultantNoteInProgram(string $programId, string $id): ConsultantNote;
}

<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\Participant\Worksheet;

interface WorksheetRepository
{

    public function allUncommentedWorksheetCommentableByPersonnel(string $personnelId, int $page, int $pageSize);

    public function allActiveWorksheetsBelongsToParticipant(string $participantId, int $page, int $pageSize);

    public function allActiveWorksheetsInProgram(string $programId, WorksheetFilter $filter);

    public function aWorksheetInProgram(string $programId, string $worksheetId): Worksheet;
}

<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\Participant\Worksheet;
use Query\Domain\Task\Dependency\PaginationFilter;

interface WorksheetRepository
{

    public function allUncommentedWorksheetCommentableByPersonnel(string $personnelId, int $page, int $pageSize);

    public function allActiveWorksheetsBelongsToParticipant(string $participantId, int $page, int $pageSize);

    public function allActiveWorksheetsInProgram(string $programId, WorksheetFilter $filter);

    public function aWorksheetInProgram(string $programId, string $worksheetId): Worksheet;

    public function uncommentedWorksheetListInProgramsCoordinatedByPersonnel(
            string $personnelId, PaginationFilter $paginationFilter);

    public function worksheetListInAllProgramsMentoredByParticipant(
            string $personnelId, WorksheetListFilterForConsultant $filter);

    public function worksheetListInAllProgramsCoordinatedByParticipant(
            string $personnelId, WorksheetListFilterForCoordinator $filter);
}

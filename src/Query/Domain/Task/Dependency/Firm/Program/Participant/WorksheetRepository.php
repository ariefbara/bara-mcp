<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant;

interface WorksheetRepository
{

    public function allUncommentedWorksheetCommentableByPersonnel(string $personnelId, int $page, int $pageSize);
}

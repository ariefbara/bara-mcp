<?php

namespace User\Application\Service\User\ProgramParticipation\Worksheet;

use User\Domain\Model\User\ProgramParticipation\Worksheet\Comment;

interface CommentRepository
{

    public function ofId(WorksheetCompositionId $worksheetCompositionId, string $commentId): Comment;
}

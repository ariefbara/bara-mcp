<?php

namespace Client\Application\Service\Client\ProgramParticipation\Worksheet;

use Client\Domain\Model\Client\ProgramParticipation\Worksheet\Comment;

interface CommentRepository
{

    public function ofId(WorksheetCompositionId $worksheetCompositionId, string $commentId): Comment;

    public function all(WorksheetCompositionId $worksheetCompositionId, int $page, int $pageSize);
}

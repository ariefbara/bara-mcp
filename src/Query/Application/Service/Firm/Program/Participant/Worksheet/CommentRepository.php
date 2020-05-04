<?php

namespace Query\Application\Service\Firm\Program\Participant\Worksheet;

use Query\Domain\Model\Firm\Program\Participant\Worksheet\Comment;


interface CommentRepository
{

    public function ofId(WorksheetCompositionId $worksheetCompositionId, string $commentId): Comment;

    public function all(WorksheetCompositionId $worksheetCompositionId, int $page, int $pageSize);
}

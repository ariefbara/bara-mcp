<?php

namespace Query\Application\Service\Client\ProgramParticipation\Worksheet;

use Client\Application\Service\Client\ProgramParticipation\Worksheet\WorksheetCompositionId;
use Query\Domain\Model\Firm\Program\Participant\Worksheet\Comment;

interface CommentRepository
{

    public function aCommentInWorksheetOfParticipant(
            WorksheetCompositionId $worksheetCompositionId, string $commentId): Comment;

    public function allCommentsInWorksheetOfParticipant(
            WorksheetCompositionId $worksheetCompositionId, int $page, int $pageSize);
}

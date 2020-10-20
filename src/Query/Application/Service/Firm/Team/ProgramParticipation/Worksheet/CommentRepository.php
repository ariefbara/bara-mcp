<?php

namespace Query\Application\Service\Firm\Team\ProgramParticipation\Worksheet;

use Query\Domain\Model\Firm\Program\Participant\Worksheet\Comment;

interface CommentRepository
{

    public function aCommentBelongsToTeam(string $teamId, string $commentId): Comment;

    public function allCommentsInWorksheetBelongsToTeam(string $teamId, string $worksheetId, int $page, int $pageSize);
}

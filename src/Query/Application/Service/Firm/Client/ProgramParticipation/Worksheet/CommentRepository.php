<?php

namespace Query\Application\Service\Firm\Client\ProgramParticipation\Worksheet;

use Query\Domain\Model\Firm\Program\Participant\Worksheet\Comment;

interface CommentRepository
{

    public function aCommentInClientWorksheet(
            string $firmId, string $clientId, string $programParticipationId, string $worksheetId, string $commentId): Comment;

    public function allCommentsInClientWorksheet(
            string $firmId, string $clientId, string $programParticipationId, string $worksheetId, int $page,
            int $pageSize);
}

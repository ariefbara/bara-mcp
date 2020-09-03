<?php

namespace Query\Application\Service\User\ProgramParticipation\Worksheet;

use Query\Domain\Model\Firm\Program\Participant\Worksheet\Comment;

interface CommentRepository
{

    public function aCommentInUserParticipantWorksheet(
            string $userId, string $userParticipantId, string $worksheetId, string $commentId): Comment;

    public function allCommentInUserParticipantWorksheet(
            string $userId, string $userParticipantId, string $worksheetId, int $page, int $pageSize);
}

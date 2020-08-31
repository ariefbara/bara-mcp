<?php

namespace Notification\Application\Service\Firm\Program\Participant\Worksheet;

use Notification\Domain\Model\Firm\Program\Participant\Worksheet\Comment;

interface CommentRepository
{

    public function aCommentInClientParticipantWorksheet(
            string $firmId, string $clientId, string $programParticipationId, string $worksheetId, string $commentId): Comment;
    
    public function aCommentInUserParticipantWorksheet(
            string $userId, string $programParticipationId, string $worksheetId, string $commentId): Comment;
}

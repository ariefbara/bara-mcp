<?php

namespace Participant\Application\Service\Participant\Worksheet;

use Participant\Domain\Model\Participant\Worksheet\Comment;

interface CommentRepository
{

    public function nextIdentity(): string;

    public function add(Comment $comment): void;

    public function aCommentInClientParticipantWorksheet(
            string $firmId, string $clientId, string $programParticipationId, string $worksheetId, string $commentId): Comment;

    public function aCommentInUserParticipantWorksheet(
            string $userId, string $programParticipationId, string $worksheetId, string $commentId): Comment;

    public function update(): void;
}

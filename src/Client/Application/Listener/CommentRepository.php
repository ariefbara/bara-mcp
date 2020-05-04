<?php

namespace Client\Application\Listener;

use Client\Domain\Model\Client\ProgramParticipation\Worksheet\Comment;

interface CommentRepository
{
    public function aCommentFromConsultant(string $firmId, string $personnelId, string $consultantId, string $consultantCommentId): Comment;
}

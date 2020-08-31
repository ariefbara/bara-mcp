<?php

namespace User\Application\Listener;

use User\Domain\Model\User\ProgramParticipation\Worksheet\Comment;

interface CommentRepository
{
    public function aCommentFromConsultant(string $firmId, string $personnelId, string $consultantId, string $consultantCommentId): Comment;
}

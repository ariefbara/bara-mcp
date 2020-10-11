<?php

namespace Notification\Application\Service;

use Notification\Domain\Model\Firm\Program\Participant\Worksheet\Comment;

interface CommentRepository
{

    public function ofId(string $commentId): Comment;

    public function update(): void;
}

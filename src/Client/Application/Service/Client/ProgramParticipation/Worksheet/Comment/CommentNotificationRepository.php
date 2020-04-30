<?php

namespace Client\Application\Service\Client\ProgramParticipation\Worksheet\Comment;

use Client\Domain\Model\Client\ProgramParticipation\Worksheet\Comment\CommentNotification;

interface CommentNotificationRepository
{

    public function nextIdentity(): string;

    public function add(CommentNotification $commentNotification): void;
}

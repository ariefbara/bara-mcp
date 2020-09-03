<?php

namespace Notification\Application\Listener\Firm\Program\Participant\Worksheet;

use Resources\Application\Event\Event;

interface CommentRepliedByConsultantEventInterface extends Event
{
    public function getFirmId(): string;
    public function getPersonnelId(): string;
    public function getProgramConsultationId(): string;
    public function getConsultantCommentId(): string;
}

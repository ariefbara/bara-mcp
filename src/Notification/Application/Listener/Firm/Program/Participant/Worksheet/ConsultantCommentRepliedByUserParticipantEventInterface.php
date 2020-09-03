<?php

namespace Notification\Application\Listener\Firm\Program\Participant\Worksheet;

use Resources\Application\Event\Event;

interface ConsultantCommentRepliedByUserParticipantEventInterface extends Event
{

    public function getUserId(): string;

    public function getProgramParticipationId(): string;

    public function getWorksheetId(): string;

    public function getCommentId(): string;
}

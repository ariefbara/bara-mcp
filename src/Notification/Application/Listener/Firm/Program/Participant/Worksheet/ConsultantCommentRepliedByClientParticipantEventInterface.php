<?php

namespace Notification\Application\Listener\Firm\Program\Participant\Worksheet;

use Resources\Application\Event\Event;

interface ConsultantCommentRepliedByClientParticipantEventInterface extends Event
{

    public function getFirmId(): string;

    public function getClientId(): string;

    public function getProgramParticipationId(): string;

    public function getWorksheetId(): string;

    public function getCommentId(): string;
}

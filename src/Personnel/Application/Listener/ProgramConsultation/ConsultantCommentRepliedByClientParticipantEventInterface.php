<?php

namespace Personnel\Application\Listener\ProgramConsultation;

use Resources\Application\Event\Event;

interface ConsultantCommentRepliedByClientParticipantEventInterface extends Event
{
    public function getFirmId(): string;
    public function getClientId(): string;
    public function getProgramId(): string;
    public function getWorksheetId(): string;
    public function getParticipantCommentId(): string;
}

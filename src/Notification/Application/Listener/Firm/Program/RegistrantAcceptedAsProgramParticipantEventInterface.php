<?php

namespace Notification\Application\Listener\Firm\Program;

use Resources\Application\Event\Event;

interface RegistrantAcceptedAsProgramParticipantEventInterface extends Event
{

    public function getFirmId(): string;

    public function getProgramId(): string;

    public function getParticipantId(): string;
}

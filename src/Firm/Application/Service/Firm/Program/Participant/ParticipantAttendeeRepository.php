<?php

namespace Firm\Application\Service\Firm\Program\Participant;

use Firm\Domain\Model\Firm\Program\Participant\ParticipantAttendee;

interface ParticipantAttendeeRepository
{
    public function ofId(string $id): ParticipantAttendee;
}

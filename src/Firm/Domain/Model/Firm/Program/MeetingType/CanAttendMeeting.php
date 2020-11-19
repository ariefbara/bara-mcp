<?php

namespace Firm\Domain\Model\Firm\Program\MeetingType;

use Firm\Domain\Model\Firm\{
    Program,
    Program\MeetingType\Meeting\Attendee
};
use SharedContext\Domain\ValueObject\ActivityParticipantType;

interface CanAttendMeeting
{

    public function roleCorrespondWith(ActivityParticipantType $role): bool;

    public function canInvolvedInProgram(Program $program): bool;

    public function registerAsAttendeeCandidate(Attendee $attendee): void;
}

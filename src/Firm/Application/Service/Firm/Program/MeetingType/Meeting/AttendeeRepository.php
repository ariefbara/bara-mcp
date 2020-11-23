<?php

namespace Firm\Application\Service\Firm\Program\MeetingType\Meeting;

use Firm\ {
    Application\Service\Client\AttendeeRepository as InterfaceForClient,
    Application\Service\Manager\AttendeeRepository as InterfaceForManager,
    Application\Service\Personnel\AttendeeRepository as InterfaceForPersonnel,
    Application\Service\User\MeetingAttendee\AttendeeRepository as InterfaceForUser,
    Domain\Model\Firm\Program\MeetingType\Meeting\Attendee
};

interface AttendeeRepository extends InterfaceForPersonnel, InterfaceForManager, InterfaceForClient, InterfaceForUser
{
    public function ofId(string $attendeeId): Attendee;
}

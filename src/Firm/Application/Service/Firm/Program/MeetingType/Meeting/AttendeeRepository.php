<?php

namespace Firm\Application\Service\Firm\Program\MeetingType\Meeting;

use Firm\Application\Service\ {
    Client\AttendeeRepository as InterfaceForClient,
    Manager\AttendeeRepository as InterfaceForManager,
    Personnel\AttendeeRepository as InterfaceForPersonnel,
    User\MeetingAttendee\AttendeeRepository as InterfaceForUser
};

interface AttendeeRepository extends InterfaceForPersonnel, InterfaceForManager, InterfaceForClient, InterfaceForUser
{
    
}

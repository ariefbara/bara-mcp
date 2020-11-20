<?php

namespace Firm\Application\Service\Firm\Program\MeetingType\Meeting;

use Firm\Application\Service\ {
    Manager\AttendeeRepository as InterfaceForManager,
    Personnel\AttendeeRepository as InterfaceForPersonnel
};

interface AttendeeRepository extends InterfaceForPersonnel, InterfaceForManager
{
    
}

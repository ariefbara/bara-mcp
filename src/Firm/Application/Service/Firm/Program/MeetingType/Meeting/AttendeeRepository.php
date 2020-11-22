<?php

namespace Firm\Application\Service\Firm\Program\MeetingType\Meeting;

use Firm\Application\Service\ {
    Client\AttendeeRepository as InterfaceForClient,
    Manager\AttendeeRepository as InterfaceForManager,
    Personnel\AttendeeRepository as InterfaceForPersonnel
};

interface AttendeeRepository extends InterfaceForPersonnel, InterfaceForManager, InterfaceForClient
{
    
}

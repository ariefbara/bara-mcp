<?php

namespace Firm\Application\Service\Firm\Program\MeetingType;

use Firm\ {
    Application\Service\Personnel\MeetingRepository as InterfaceForPersonnel,
    Domain\Model\Firm\Program\MeetingType\Meeting
};

interface MeetingRepository extends InterfaceForPersonnel
{
    public function nextIdentity(): string;
    
    public function add(Meeting $meeting);
}

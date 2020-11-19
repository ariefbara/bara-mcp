<?php

namespace Firm\Application\Service\Personnel;

use Firm\Domain\Model\Firm\Program\MeetingType\Meeting;

interface MeetingRepository
{
    public function nextIdentity(): string;
    
    public function add(Meeting $meeting);
}

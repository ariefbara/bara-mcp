<?php

namespace Firm\Domain\Task\Shared\Firm\Program\ActivityType;

use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;

interface MeetingRepository
{
    public function nextIdentity(): string;
    
    public function add(Meeting $meeting): void;
    
}

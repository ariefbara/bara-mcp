<?php

namespace Notification\Application\Service;

use Notification\Domain\Model\Firm\Program\MeetingType\Meeting;

interface MeetingRepository
{
    public function ofId(string $meetingId): Meeting;
    
    public function update(): void;
}

<?php

namespace Firm\Domain\Model\Firm\Program\ActivityType\Meeting;

use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;

interface ITaskExecutableByMeetingInitiator
{
    public function executeByMeetingInitiatorOf(Meeting $meeting): void;
}

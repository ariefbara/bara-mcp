<?php

namespace Firm\Domain\Task\MeetingInitiator;

use Firm\Domain\Model\Firm\Program\CanAttendMeeting;

interface UserRepository
{
    public function aUserOfId(string $id): CanAttendMeeting;
}

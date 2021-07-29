<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;

interface CanAttendMeeting
{

    public function inviteToMeeting(Meeting $meeting): void;

}

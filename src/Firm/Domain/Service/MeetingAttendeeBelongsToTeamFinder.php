<?php

namespace Firm\Domain\Service;

use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\Attendee;
use Firm\Domain\Model\Firm\Team;

class MeetingAttendeeBelongsToTeamFinder
{

    /**
     *
     * @var MeetingAttendeeRepository
     */
    protected $meetingAttendeeRepository;

    function __construct(MeetingAttendeeRepository $meetingAttendeeRepository)
    {
        $this->meetingAttendeeRepository = $meetingAttendeeRepository;
    }

    public function execute(Team $team, string $meetingId): Attendee
    {
        return $this->meetingAttendeeRepository
                        ->anAttendeeBelongsToTeamCorrespondWithMeeting($team->getId(), $meetingId);
    }

}

<?php

namespace Firm\Domain\Model\Firm\Program\MeetingType\Meeting\Attendee;

use Firm\Domain\Model\Firm\Program\ {
    Coordinator,
    MeetingType\CanAttendMeeting,
    MeetingType\Meeting\Attendee
};

class CoordinatorAttendee
{

    /**
     *
     * @var Attendee
     */
    protected $attendee;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Coordinator
     */
    protected $coordinator;

    function __construct(Attendee $attendee, string $id, Coordinator $coordinator)
    {
        $this->attendee = $attendee;
        $this->id = $id;
        $this->coordinator = $coordinator;
    }
    
    public function coordinatorEquals(CanAttendMeeting $user): bool
    {
        return $this->coordinator === $user;
    }
    
    public function disableValidInvitation(): void
    {
        $this->attendee->disableValidInvitation();
    }

}

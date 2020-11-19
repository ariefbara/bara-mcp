<?php

namespace Firm\Domain\Model\Firm\Program\MeetingType\Meeting\Attendee;

use Firm\Domain\Model\Firm\ {
    Manager,
    Program\MeetingType\CanAttendMeeting,
    Program\MeetingType\Meeting\Attendee
};

class ManagerAttendee
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
     * @var Manager
     */
    protected $manager;

    function __construct(Attendee $attendee, string $id, Manager $manager)
    {
        $this->attendee = $attendee;
        $this->id = $id;
        $this->manager = $manager;
    }
    
    public function managerEquals(CanAttendMeeting $user): bool
    {
        return $this->manager === $user;
    }

}

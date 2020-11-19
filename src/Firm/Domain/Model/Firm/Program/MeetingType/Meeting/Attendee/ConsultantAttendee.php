<?php

namespace Firm\Domain\Model\Firm\Program\MeetingType\Meeting\Attendee;

use Firm\Domain\Model\Firm\Program\ {
    Consultant,
    MeetingType\CanAttendMeeting,
    MeetingType\Meeting\Attendee
};

class ConsultantAttendee
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
     * @var Consultant
     */
    protected $consultant;

    function __construct(Attendee $attendee, string $id, Consultant $consultant)
    {
        $this->attendee = $attendee;
        $this->id = $id;
        $this->consultant = $consultant;
    }
    
    public function consultantEquals(CanAttendMeeting $user): bool
    {
        return $this->consultant === $user;
    }

}

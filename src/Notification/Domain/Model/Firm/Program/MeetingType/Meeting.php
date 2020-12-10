<?php

namespace Notification\Domain\Model\Firm\Program\MeetingType;

use Doctrine\Common\Collections\ArrayCollection;
use Notification\Domain\Model\Firm\Program\MeetingType;
use Resources\Domain\ValueObject\DateTimeInterval;

class Meeting
{

    /**
     *
     * @var MeetingType
     */
    protected $meetingType;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var DateTimeInterval
     */
    protected $startEndTime;

    /**
     *
     * @var string|null
     */
    protected $location;

    /**
     *
     * @var bool
     */
    protected $cancelled;

    /**
     * 
     * @var ArrayCollection
     */
    protected $attendees;

    protected function __construct()
    {
        
    }

    public function addMeetingCreatedNotification(): void
    {
        
    }

    public function addMeetingScheduleChangedNotification(): void
    {
        
    }

}

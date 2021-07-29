<?php

namespace Firm\Domain\Model\Firm\Manager;

use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\Attendee;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;
use SharedContext\Domain\ValueObject\ActivityParticipantType;

class ManagerAttendee
{

    /**
     * 
     * @var Manager
     */
    protected $manager;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var Attendee
     */
    protected $attendee;
    
    public function __construct(Manager $manager, string $id, Meeting $meeting, bool $anInitiator)
    {
        $this->manager = $manager;
        $this->id = $id;
        $activityParticipantType = new ActivityParticipantType(ActivityParticipantType::MANAGER);
        $this->attendee = $meeting->createAttendee($id, $activityParticipantType, $anInitiator);
    }
    
    public function isActiveAttendeeOfMeeting(Meeting $meeting): bool
    {
        return $this->attendee->isActiveAttendeeOfMeeting($meeting);
    }
    
    public function executeTaskAsMeetingInitiator(ITaskExecutableByMeetingInitiator $task): void
    {
        $this->attendee->executeTaskAsMeetingInitiator($task);
    }
    
}

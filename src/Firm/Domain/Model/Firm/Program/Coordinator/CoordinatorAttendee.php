<?php

namespace Firm\Domain\Model\Firm\Program\Coordinator;

use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\Attendee;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;
use Firm\Domain\Model\Firm\Program\Coordinator;
use SharedContext\Domain\ValueObject\ActivityParticipantType;

class CoordinatorAttendee
{

    /**
     * 
     * @var Coordinator
     */
    protected $coordinator;

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
    
    public function __construct(Coordinator $coordinator, string $id, Meeting $meeting, bool $anInitiator)
    {
        $this->coordinator = $coordinator;
        $this->id = $id;
        $activityParticipantType = new ActivityParticipantType(ActivityParticipantType::COORDINATOR);
        $this->attendee = $meeting->createAttendee($id, $activityParticipantType, $anInitiator);
    }
    
    public function isActiveAttendeeOfMeeting(Meeting $meeting): bool
    {
        return $this->attendee->isActiveAttendeeOfMeeting($meeting);
    }
    
    public function disableValidInvitation(): void
    {
        $this->attendee->disableValidInvitation();
    }
    
    public function executeTaskAsMeetingInitiator(ITaskExecutableByMeetingInitiator $task): void
    {
        $this->attendee->executeTaskAsMeetingInitiator($task);
    }
    
}

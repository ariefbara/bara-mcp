<?php

namespace Firm\Domain\Model\Firm\Program\ActivityType\Meeting;

use Config\EventList;
use Firm\Domain\Model\Firm\Program\ActivityType\ActivityParticipant;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Resources\Domain\Event\CommonEvent;
use Resources\Domain\Model\EntityContainEvents;
use Resources\Exception\RegularException;

class Attendee extends EntityContainEvents
{

    /**
     *
     * @var Meeting
     */
    protected $meeting;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var ActivityParticipant
     */
    protected $attendeeSetup;

    /**
     *
     * @var bool
     */
    protected $anInitiator;

    /**
     *
     * @var bool|null
     */
    protected $willAttend;

    /**
     *
     * @var bool|null
     */
    protected $attended;

    /**
     *
     * @var bool
     */
    protected $cancelled;

    public function getMeeting(): Meeting
    {
        return $this->meeting;
    }
    
    public function __construct(Meeting $meeting, string $id, ActivityParticipant $attendeeSetup, bool $anInitiator)
    {
        if ($anInitiator) {
            $attendeeSetup->assertCanInitiate();
        } else {
            $attendeeSetup->assertCanAttend();
        }
        $this->meeting = $meeting;
        $this->id = $id;
        $this->attendeeSetup = $attendeeSetup;
        $this->anInitiator = $anInitiator;
        $this->willAttend = $anInitiator ?: false;
        $this->attended = false;
        $this->cancelled = false;
        
        $this->recordEvent(new CommonEvent(EventList::MEETING_INVITATION_SENT, $this->id));
    }
    
    public function assertActiveInitiator(): void
    {
        if ($this->cancelled || !$this->anInitiator) {
            throw RegularException::forbidden('forbidden: not an active meeting initiator');
        }
    }
    
    public function cancel(): void
    {
        if ($this->anInitiator) {
            $errorDetail = "forbidden: cannot cancel invitationt to initiator";
            throw RegularException::forbidden($errorDetail);
        }
        $this->cancelled = true;

        $event = new CommonEvent(EventList::MEETING_INVITATION_CANCELLED, $this->id);
        $this->recordEvent($event);
    }
    
    public function isActiveAttendeeOfMeeting(Meeting $meeting): bool
    {
        return !$this->cancelled && $this->meeting === $meeting;
    }
    
    public function disableValidInvitation(): void
    {
        if ($this->meeting->isUpcoming()) {
            $this->cancelled = true;
        }
    }
    
    public function executeTaskAsMeetingInitiator(ITaskExecutableByMeetingInitiator $taks): void
    {
        $this->assertActiveInitiator();
        $taks->executeByMeetingInitiatorOf($this->meeting);
    }
    
    public function assertManageableInMeeting(Meeting $meeting): void
    {
        if ($this->cancelled || $this->meeting !== $meeting) {
            throw RegularException::forbidden("forbidden: unamanged attendee");
        }
    }
    
}

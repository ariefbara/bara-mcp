<?php

namespace Firm\Domain\Model\Firm\Program\MeetingType\Meeting;

use Config\EventList;
use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Model\Firm\Program\ActivityType\ActivityParticipant;
use Firm\Domain\Model\Firm\Program\Consultant;
use Firm\Domain\Model\Firm\Program\Coordinator;
use Firm\Domain\Model\Firm\Program\MeetingType\CanAttendMeeting;
use Firm\Domain\Model\Firm\Program\MeetingType\Meeting;
use Firm\Domain\Model\Firm\Program\MeetingType\Meeting\Attendee\ConsultantAttendee;
use Firm\Domain\Model\Firm\Program\MeetingType\Meeting\Attendee\CoordinatorAttendee;
use Firm\Domain\Model\Firm\Program\MeetingType\Meeting\Attendee\ManagerAttendee;
use Firm\Domain\Model\Firm\Program\MeetingType\Meeting\Attendee\ParticipantAttendee;
use Firm\Domain\Model\Firm\Program\MeetingType\MeetingData;
use Firm\Domain\Model\Firm\Program\Participant;
use Firm\Domain\Model\Firm\Team;
use Resources\Domain\Event\CommonEvent;
use Resources\Domain\Model\EntityContainCommonEvents;
use Resources\Exception\RegularException;

class Attendee extends EntityContainCommonEvents
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

    /**
     *
     * @var ManagerAttendee|null
     */
    protected $managerAttendee;

    /**
     *
     * @var CoordinatorAttendee|null
     */
    protected $coordinatorAttendee;

    /**
     *
     * @var ConsultantAttendee|null
     */
    protected $consultantAttendee;

    /**
     *
     * @var ParticipantAttendee|null
     */
    protected $participantAttendee;

    function __construct(
            Meeting $meeting, string $id, ActivityParticipant $attendeeSetup, CanAttendMeeting $user,
            bool $anInitiator = false)
    {
        $this->meeting = $meeting;
        $this->id = $id;
        $this->attendeeSetup = $attendeeSetup;
        $this->anInitiator = $anInitiator;
        $this->willAttend = $this->anInitiator ? true : null;
        $this->attended = null;
        $this->cancelled = false;

        $user->registerAsAttendeeCandidate($this);
        
        $event = new CommonEvent(EventList::MEETING_INVITATION_SENT, $this->id);
        $this->recordEvent($event);
    }
    
    public function belongsToTeam(Team $team): bool
    {
        return isset($this->participantAttendee)? $this->participantAttendee->belongsToTeam($team): false;
    }

    public function meetingEquals(Meeting $meeting): bool
    {
        return $this->meeting === $meeting;
    }

    public function updateMeeting(MeetingData $meetingData): void
    {
        $this->assertInitiator();
        $this->meeting->update($meetingData);
        $this->recordedEvents = $this->meeting->pullRecordedEvents();
    }

    public function inviteUserToAttendMeeting(CanAttendMeeting $user): void
    {
        $this->assertInitiator();
        $this->meeting->inviteUser($user);
        $this->recordedEvents = $this->meeting->pullRecordedEvents();
    }

    public function cancelInvitationTo(Attendee $attendee): void
    {
        $this->assertInitiator();
        if (!$attendee->meetingEquals($this->meeting)) {
            $errorDetail = "forbidden: not allow to manage attendee of other meeting";
            throw RegularException::forbidden($errorDetail);
        }
        $attendee->cancel();
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

    public function reinvite(): void
    {
        if (!$this->cancelled) {
            $errorDetail = "forbidden: user already receive inivitation";
            throw RegularException::forbidden($errorDetail);
        }
        $this->cancelled = false;
        
        $event = new CommonEvent(EventList::MEETING_INVITATION_SENT, $this->id);
        $this->recordEvent($event);
    }

    public function correspondWithUser(CanAttendMeeting $user): bool
    {
        if (isset($this->managerAttendee)) {
            return $this->managerAttendee->managerEquals($user);
        } elseif (isset($this->coordinatorAttendee)) {
            return $this->coordinatorAttendee->coordinatorEquals($user);
        } elseif (isset($this->consultantAttendee)) {
            return $this->consultantAttendee->consultantEquals($user);
        } else {
            return $this->participantAttendee->participantEquals($user);
        }
    }

    public function setManagerAsAttendeeCandidate(Manager $manager): void
    {
        $this->managerAttendee = new ManagerAttendee($this, $this->id, $manager);
    }

    public function setCoordinatorAsAttendeeCandidate(Coordinator $coordinator): void
    {
        $this->coordinatorAttendee = new CoordinatorAttendee($this, $this->id, $coordinator);
    }

    public function setConsultantAsAttendeeCandidate(Consultant $consultant): void
    {
        $this->consultantAttendee = new ConsultantAttendee($this, $this->id, $consultant);
    }

    public function setParticipantAsAttendeeCandidate(Participant $participant): void
    {
        $this->participantAttendee = new ParticipantAttendee($this, $this->id, $participant);
    }

    protected function assertInitiator(): void
    {
        if (!$this->anInitiator || $this->cancelled) {
            $errorDetail = "forbidden: only active meeting initiator can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }
    
    public function disableValidInvitation(): void
    {
        if ($this->meeting->isUpcoming()) {
            $this->cancelled = true;
        }
    }

}

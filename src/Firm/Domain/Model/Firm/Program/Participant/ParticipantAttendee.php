<?php

namespace Firm\Domain\Model\Firm\Program\Participant;

use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\Attendee;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;
use Firm\Domain\Model\Firm\Program\Participant;
use Resources\Exception\RegularException;
use SharedContext\Domain\ValueObject\ActivityParticipantType;

class ParticipantAttendee
{

    /**
     * 
     * @var Participant
     */
    protected $participant;

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

    public function __construct(Participant $participant, string $id, Meeting $meeting, bool $anInitiator)
    {
        $this->participant = $participant;
        $this->id = $id;
        $activityParticipantType = new ActivityParticipantType(ActivityParticipantType::PARTICIPANT);
        $this->attendee = $meeting->createAttendee($id, $activityParticipantType, $anInitiator);
    }
    
    public function assertBelongsToParticipant(Participant $participant): void
    {
        if ($this->participant !== $participant) {
            throw RegularException::forbidden("forbidden: attendee doesn't belongs to participant");
        }
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

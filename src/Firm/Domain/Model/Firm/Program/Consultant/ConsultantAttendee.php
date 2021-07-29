<?php

namespace Firm\Domain\Model\Firm\Program\Consultant;

use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\Attendee;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;
use Firm\Domain\Model\Firm\Program\Consultant;
use Resources\Domain\Model\ContainAggregatedEntitiesHavingEvents;
use SharedContext\Domain\ValueObject\ActivityParticipantType;

class ConsultantAttendee extends ContainAggregatedEntitiesHavingEvents
{

    /**
     * 
     * @var Consultant
     */
    protected $consultant;

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
    
    public function __construct(Consultant $consultant, string $id, Meeting $meeting, bool $anInitiator)
    {
        $this->consultant = $consultant;
        $this->id = $id;
        $activityParticipantType = new ActivityParticipantType(ActivityParticipantType::CONSULTANT);
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

    public function inviteAllActiveDedicatedMentees(): void
    {
        $this->attendee->assertActiveInitiator();
        $this->consultant->inviteAllActiveDedicatedMenteesToMeeting($this->attendee->getMeeting());
        
        $this->recordEntityHavingEvents($this->consultant);
    }
    
    public function executeTaskAsMeetingInitiator(ITaskExecutableByMeetingInitiator $task): void
    {
        $this->attendee->executeTaskAsMeetingInitiator($task);
    }

}

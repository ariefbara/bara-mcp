<?php

namespace Firm\Domain\Model\Firm\Program\ActivityType;

use Config\EventList;
use DateTimeImmutable;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\ActivityType;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\Attendee;
use Resources\DateTimeImmutableBuilder;
use Resources\Domain\Event\CommonEvent;
use Resources\Domain\Model\ContainAggregatedEntitiesHavingEvents;
use Resources\Domain\ValueObject\DateTimeInterval;
use Resources\Exception\RegularException;
use Resources\ValidationRule;
use Resources\ValidationService;
use SharedContext\Domain\ValueObject\ActivityParticipantType;

class Meeting extends ContainAggregatedEntitiesHavingEvents
{

    /**
     *
     * @var string
     */
    protected $id;
    
    /**
     *
     * @var ActivityType
     */
    protected $meetingType;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var string|null
     */
    protected $description;

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
     * @var string|null
     */
    protected $note;

    /**
     *
     * @var bool
     */
    protected $cancelled;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $createdTime;

    protected function setName(string $name): void
    {
        $errorDetail = "bad request: meeting name is mandatory";
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, $errorDetail);
        $this->name = $name;
    }

    protected function setStartEndTime(?DateTimeImmutable $startTime, ?DateTimeImmutable $endTime)
    {
        if (!isset($startTime)) {
            $errorDetail = "bad request: meeting start time is mandatory";
            throw RegularException::badRequest($errorDetail);
        }
        if (!isset($endTime)) {
            $errorDetail = "bad request: meeting end time is mandatory";
            throw RegularException::badRequest($errorDetail);
        }
        $this->startEndTime = new DateTimeInterval($startTime, $endTime);
    }
    
    protected function assertUpcoming(): void
    {
        if (!$this->startEndTime->isUpcoming()) {
            throw RegularException::forbidden('forbidden: not an upcoming meeting');
        }
    }
    
    public function __construct(ActivityType $meetingType, string $id, MeetingData $meetingData)
    {
        $this->meetingType = $meetingType;
        $this->id = $id;
        $this->setName($meetingData->getName());
        $this->description = $meetingData->getDescription();
        $this->setStartEndTime($meetingData->getStartTime(), $meetingData->getEndTime());
        $this->location = $meetingData->getLocation();
        $this->note = $meetingData->getNote();
        $this->cancelled = false;
        $this->createdTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        
        $this->recordEvent(new CommonEvent(EventList::MEETING_CREATED, $this->id));
    }
    
    public function assertUsableInProgram(Program $program): void
    {
        if ($this->cancelled || !$this->startEndTime->isUpcoming() || !$this->meetingType->belongsToProgram($program)) {
            throw RegularException::forbidden('forbidden: unuseable meeting');
        }
    }
    
    public function assertUsableInFirm(Firm $firm): void
    {
        if ($this->cancelled || !$this->startEndTime->isUpcoming() || !$this->meetingType->belongsToFirm($firm)) {
            throw RegularException::forbidden('forbidden: unuseable meeting');
        }
    }
    
    public function isUpcoming(): bool
    {
        return $this->startEndTime->isUpcoming();
    }
    
    public function update(MeetingData $meetingData): void
    {
        $newSchedule = new DateTimeInterval($meetingData->getStartTime(), $meetingData->getEndTime());
        if (!$this->startEndTime->sameValueAs($newSchedule)) {
            $commonEvent = new CommonEvent(EventList::MEETING_SCHEDULE_CHANGED, $this->id);
            $this->recordEvent($commonEvent);
        }
        
        $this->setName($meetingData->getName());
        $this->description = $meetingData->getDescription();
        $this->setStartEndTime($meetingData->getStartTime(), $meetingData->getEndTime());
        $this->location = $meetingData->getLocation();
        $this->note = $meetingData->getNote();
    }
    
    public function createAttendee(string $attendeeId, ActivityParticipantType $activityParticipantType, bool $anInitiator): Attendee
    {
        $attendeeSetup = $this->meetingType->getActiveAttendeeSetupCorrenspondWithRoleOrDie($activityParticipantType);
        $attendee = new Attendee($this, $attendeeId, $attendeeSetup, $anInitiator);
        $this->recordEntityHavingEvents($attendee);
        return $attendee;
    }
    
    public function inviteAllActiveProgramParticipants(): void
    {
        $this->assertUpcoming();
        $this->meetingType->inviteAllActiveProgramParticipantsToMeeting($this);
    }

}

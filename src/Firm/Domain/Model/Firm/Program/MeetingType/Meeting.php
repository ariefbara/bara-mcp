<?php

namespace Firm\Domain\Model\Firm\Program\MeetingType;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\Firm\Program\ {
    ActivityType,
    ActivityType\ActivityParticipant,
    MeetingType\Meeting\Attendee
};
use Resources\ {
    DateTimeImmutableBuilder,
    Domain\ValueObject\DateTimeInterval,
    Exception\RegularException,
    Uuid,
    ValidationRule,
    ValidationService
};

class Meeting
{

    /**
     *
     * @var ActivityType
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

    /**
     *
     * @var ArrayCollection
     */
    protected $attendees;

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

    function __construct(ActivityType $meetingType, string $id, MeetingData $meetingData, CanAttendMeeting $initiator)
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
        
        $this->attendees = new ArrayCollection();
        
        $this->meetingType->setUserAsInitiatorInMeeting($this, $initiator);
    }

    public function update(MeetingData $meetingData): void
    {
        $this->setName($meetingData->getName());
        $this->description = $meetingData->getDescription();
        $this->setStartEndTime($meetingData->getStartTime(), $meetingData->getEndTime());
        $this->location = $meetingData->getLocation();
        $this->note = $meetingData->getNote();
    }
    
    public function inviteUser(CanAttendMeeting $user): void
    {
        $this->meetingType->addUserAsAttendeeInMeeting($this, $user);
    }
    
    public function setInitiator(ActivityParticipant $attendeeSetup, CanAttendMeeting $user): void
    {
        $id = Uuid::generateUuid4();
        $initiator = new Attendee($this, $id, $attendeeSetup, $user, true);
        $this->attendees->add($initiator);
    }
    
    public function addAttendee(ActivityParticipant $attendeeSetup, CanAttendMeeting $user): void
    {
        if (!empty($attendee = $this->findAttendeeCorrespondWithUser($user))) {
            $attendee->reinvite();
        } else {
            $id = Uuid::generateUuid4();
            $attendee = new Attendee($this, $id, $attendeeSetup, $user);
            $this->attendees->add($attendee);
        }
    }
    
    protected function findAttendeeCorrespondWithUser(CanAttendMeeting $user): ?Attendee
    {
        $p = function (Attendee $attendee) use ($user) {
            return $attendee->correspondWithUser($user);
        };
        $attendee = $this->attendees->filter($p)->first();
        return empty($attendee) ? null : $attendee;
    }
    
    public function isUpcoming(): bool
    {
        return $this->startEndTime->isUpcoming();
    }
    
}

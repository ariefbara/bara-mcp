<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\ {
    Personnel,
    Program,
    Program\MeetingType\CanAttendMeeting,
    Program\MeetingType\Meeting,
    Program\MeetingType\Meeting\Attendee,
    Program\MeetingType\MeetingData
};
use Resources\Exception\RegularException;
use SharedContext\Domain\ValueObject\ActivityParticipantType;

class Consultant implements CanAttendMeeting
{

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Personnel
     */
    protected $personnel;

    /**
     *
     * @var bool
     */
    protected $removed;

    function getPersonnel(): Personnel
    {
        return $this->personnel;
    }

    function getId(): string
    {
        return $this->id;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    function __construct(Program $program, string $id, Personnel $personnel)
    {
        $this->program = $program;
        $this->id = $id;
        $this->personnel = $personnel;
        $this->removed = false;
    }

    public function reassign(): void
    {
        $this->removed = false;
    }

    public function remove(): void
    {
        $this->removed = true;
    }
    
    public function getPersonnelName(): string
    {
        return $this->personnel->getName();
    }

    public function canInvolvedInProgram(Program $program): bool
    {
        return !$this->removed && $this->program === $program;
    }

    public function registerAsAttendeeCandidate(Attendee $attendee): void
    {
        $attendee->setConsultantAsAttendeeCandidate($this);
    }

    public function roleCorrespondWith(ActivityParticipantType $role): bool
    {
        return $role->isConsultantType();
    }
    
    public function initiateMeeting(string $meetingId, ActivityType $meetingType, MeetingData $meetingData): Meeting
    {
        if ($this->removed) {
            $errorDetail = "forbidden: only active consultant can make this request";
            throw RegularException::forbidden($errorDetail);
        }
        
        if (!$meetingType->belongsToProgram($this->program)) {
            $errorDetail = "forbidden: can only manage meeting type from same program";
            throw RegularException::forbidden($errorDetail);
        }
        return $meetingType->createMeeting($meetingId, $meetingData, $this);
    }

}

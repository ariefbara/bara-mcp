<?php

namespace Firm\Domain\Model\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\ {
    AssetBelongsToFirm,
    Firm,
    Firm\Personnel,
    Firm\Program,
    Firm\Program\MeetingType\CanAttendMeeting,
    Firm\Program\MeetingType\Meeting,
    Firm\Program\MeetingType\Meeting\Attendee,
    Firm\Program\MeetingType\MeetingData
};
use Resources\Exception\RegularException;
use SharedContext\Domain\ValueObject\ActivityParticipantType;

class Consultant implements CanAttendMeeting, AssetBelongsToFirm
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
    protected $active;
    
    /**
     *
     * @var ArrayCollection
     */
    protected $meetingInvitations;
    
    /**
     *
     * @var ArrayCollection
     */
    protected $consultationRequests;
    
    /**
     *
     * @var ArrayCollection
     */
    protected $consultationSessions;

    function getPersonnel(): Personnel
    {
        return $this->personnel;
    }

    function getId(): string
    {
        return $this->id;
    }

    function __construct(Program $program, string $id, Personnel $personnel)
    {
        $this->program = $program;
        $this->id = $id;
        $this->personnel = $personnel;
        $this->active = true;
    }

    public function enable(): void
    {
        $this->active = true;
    }

    public function disable(): void
    {
        $this->active = false;
        foreach ($this->meetingInvitations->getIterator() as $meetingInvitation) {
            $meetingInvitation->disableValidInvitation();
        }
        foreach ($this->consultationRequests->getIterator() as $consultationRequest) {
            $consultationRequest->disableUpcomingRequest();
        }
        foreach ($this->consultationSessions->getIterator() as $consultationSession) {
            $consultationSession->disableUpcomingSession();
        }
    }

    public function getPersonnelName(): string
    {
        return $this->personnel->getName();
    }

    public function canInvolvedInProgram(Program $program): bool
    {
        return $this->active && $this->program === $program;
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
        if (!$this->active) {
            $errorDetail = "forbidden: only active consultant can make this request";
            throw RegularException::forbidden($errorDetail);
        }

        if (!$meetingType->belongsToProgram($this->program)) {
            $errorDetail = "forbidden: can only manage meeting type from same program";
            throw RegularException::forbidden($errorDetail);
        }
        return $meetingType->createMeeting($meetingId, $meetingData, $this);
    }

    public function belongsToFirm(Firm $firm): bool
    {
        return $this->program->belongsToFirm($firm);
    }

}

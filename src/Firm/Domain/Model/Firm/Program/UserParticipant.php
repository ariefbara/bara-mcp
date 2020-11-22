<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Program\MeetingType\ {
    Meeting,
    MeetingData
};

class UserParticipant
{

    /**
     *
     * @var Participant
     */
    protected $participant;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $userId;
    
    public function __construct(Participant $participant, string $id, string $userId)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->userId = $userId;
    }
    
    public function correspondWithRegistrant(Registrant $registrant): bool
    {
        return $registrant->correspondWithUser($this->userId);
    }
    
    public function initiateMeeting(string $meetingId, ActivityType $meetingType, MeetingData $meetingData): Meeting
    {
        return $this->participant->initiateMeeting($meetingId, $meetingType, $meetingData);
    }
    
}

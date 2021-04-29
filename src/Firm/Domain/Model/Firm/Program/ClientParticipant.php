<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Program\MeetingType\ {
    Meeting,
    MeetingData
};

class ClientParticipant
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
    protected $clientId;
    
    public function __construct(Participant $participant, string $id, string $clientId)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->clientId = $clientId;
    }
    
    public function correspondWithRegistrant(Registrant $registrant): bool
    {
        return $registrant->correspondWithClient($this->clientId);
    }
    
    public function initiateMeeting(string $meetingId, ActivityType $meetingType, MeetingData $meetingData): Meeting
    {
        return $this->participant->initiateMeeting($meetingId, $meetingType, $meetingData);
    }
    
    public function submitCommentInMission(Mission $mission, string $missionCommentId, string $message): Mission\MissionComment
    {
        
    }

}

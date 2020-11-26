<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\ {
    Program\MeetingType\Meeting,
    Program\MeetingType\MeetingData,
    Team
};

class TeamParticipant
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
    protected $teamId;

    public function __construct(Participant $participant, string $id, string $teamId)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->teamId = $teamId;
    }

    public function correspondWithRegistrant(Registrant $registrant): bool
    {
        return $registrant->correspondWithTeam($this->teamId);
    }
    
    public function belongsToTeam(Team $team): bool
    {
        return $team->idEquals($this->teamId);
    }
    
    public function initiateMeeting(string $meetingId, ActivityType $meetingType, MeetingData $meetingData): Meeting
    {
        return $this->participant->initiateMeeting($meetingId, $meetingType, $meetingData);
    }

}

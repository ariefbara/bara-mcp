<?php

namespace Firm\Domain\Model\Firm\Team;

use Firm\Domain\ {
    Model\Firm\Client,
    Model\Firm\Program\ActivityType,
    Model\Firm\Program\MeetingType\CanAttendMeeting,
    Model\Firm\Program\MeetingType\Meeting,
    Model\Firm\Program\MeetingType\Meeting\Attendee,
    Model\Firm\Program\MeetingType\MeetingData,
    Model\Firm\Program\TeamParticipant,
    Model\Firm\Team,
    Service\MeetingAttendeeBelongsToTeamFinder
};
use Resources\Exception\RegularException;

class Member
{

    /**
     *
     * @var Team
     */
    protected $team;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Client
     */
    protected $client;

    /**
     *
     * @var bool
     */
    protected $anAdmin;

    /**
     *
     * @var bool
     */
    protected $active;

    protected function __construct()
    {
        
    }

    public function initiateMeeting(
            string $meetingId, TeamParticipant $teamParticipant, ActivityType $meetingType, MeetingData $meetingData): Meeting
    {
        $this->assertActive();
        if (!$teamParticipant->belongsToTeam($this->team)) {
            $errorDetail = "forbidden: can only manage program participation belongs to same team";
            throw RegularException::forbidden($errorDetail);
        }
        return $teamParticipant->initiateMeeting($meetingId, $meetingType, $meetingData);
    }

    public function updateMeeting(
            MeetingAttendeeBelongsToTeamFinder $meetingAttendeeFinder, string $meetingId, MeetingData $meetingData): void
    {
        $this->assertActive();
        $meetingAttendeeFinder->execute($this->team, $meetingId)
                ->updateMeeting($meetingData);
    }

    public function inviteUserToAttendMeeting(
            MeetingAttendeeBelongsToTeamFinder $meetingAttendeeFinder, string $meetingId, CanAttendMeeting $user): void
    {
        $this->assertActive();
        $meetingAttendeeFinder->execute($this->team, $meetingId)
                ->inviteUserToAttendMeeting($user);
    }
    
    public function cancelInvitation(
            MeetingAttendeeBelongsToTeamFinder $meetingAttendeeFinder, string $meetingId, Attendee $attendee): void
    {
        $this->assertActive();
        $meetingAttendeeFinder->execute($this->team, $meetingId)
                ->cancelInvitationTo($attendee);
    }

    protected function assertActive(): void
    {
        if (!$this->active) {
            $errorDetail = "forbidden: only active team member can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

}

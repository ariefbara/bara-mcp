<?php

namespace Firm\Domain\Model\Firm\Team;

use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Model\Firm\Program\ActivityType;
use Firm\Domain\Model\Firm\Program\MeetingType\CanAttendMeeting;
use Firm\Domain\Model\Firm\Program\MeetingType\Meeting;
use Firm\Domain\Model\Firm\Program\MeetingType\Meeting\Attendee;
use Firm\Domain\Model\Firm\Program\MeetingType\MeetingData;
use Firm\Domain\Model\Firm\Program\Mission;
use Firm\Domain\Model\Firm\Program\Mission\MissionComment;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;
use Firm\Domain\Model\Firm\Program\TeamParticipant;
use Firm\Domain\Model\Firm\Team;
use Firm\Domain\Service\MeetingAttendeeBelongsToTeamFinder;
use Resources\Domain\Model\EntityContainCommonEvents;
use Resources\Exception\RegularException;

class Member extends EntityContainCommonEvents
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
        $meeting = $meetingAttendeeFinder->execute($this->team, $meetingId);
        $meeting->updateMeeting($meetingData);

        $this->recordedEvents = $meeting->pullRecordedEvents();
    }

    public function inviteUserToAttendMeeting(
            MeetingAttendeeBelongsToTeamFinder $meetingAttendeeFinder, string $meetingId, CanAttendMeeting $user): void
    {
        $this->assertActive();
        $meeting = $meetingAttendeeFinder->execute($this->team, $meetingId);
        $meeting->inviteUserToAttendMeeting($user);

        $this->recordedEvents = $meeting->pullRecordedEvents();
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
    protected function assertManageableTeamParticipant(TeamParticipant $teamParticipant): void
    {
        if (! $teamParticipant->belongsToTeam($this->team)) {
            throw RegularException::forbidden('forbidden: unable to manage team participant');
        }
    }

    public function submitCommentInMission(
            TeamParticipant $teamParticipant, Mission $mission, string $missionCommentId,
            MissionCommentData $missionCommentData): MissionComment
    {
        $this->assertActive();
        $this->assertManageableTeamParticipant($teamParticipant);
        $missionCommentData->addRolePath('member', $this->id);
        return $teamParticipant->submitCommentInMission($mission, $missionCommentId, $missionCommentData, $this->client);
    }

    public function replyMissionComment(
            TeamParticipant $teamParticipant, MissionComment $missionComment, string $replyId,
            MissionCommentData $missionCommentData): MissionComment
    {
        $this->assertActive();
        $this->assertManageableTeamParticipant($teamParticipant);
        $missionCommentData->addRolePath('member', $this->id);
        return $teamParticipant->replyMissionComment($missionComment, $replyId, $missionCommentData, $this->client);
    }

}

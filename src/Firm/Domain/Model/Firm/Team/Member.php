<?php

namespace Firm\Domain\Model\Firm\Team;

use DateTimeImmutable;
use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Model\Firm\Program\ActivityType;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingData;
use Firm\Domain\Model\Firm\Program\Mission;
use Firm\Domain\Model\Firm\Program\Mission\MissionComment;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;
use Firm\Domain\Model\Firm\Program\Participant\ParticipantAttendee;
use Firm\Domain\Model\Firm\Program\TeamParticipant;
use Firm\Domain\Model\Firm\Team;
use Resources\DateTimeImmutableBuilder;
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
    
    /**
     *
     * @var string||null
     */
    protected $position;
    
    /**
     *
     * @var DateTimeImmutable
     */
    protected $joinTime;

    public function __construct(Team $team, string $id, MemberData $memberData)
    {
        $this->team = $team;
        $this->id = $id;
        $this->client = $memberData->getClient();
        $this->anAdmin = true;
        $this->active = true;
        $this->position = $memberData->getPosition();
        $this->joinTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
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

    protected function assertActive(): void
    {
        if (!$this->active) {
            $errorDetail = "forbidden: only active team member can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

    protected function assertManageableTeamParticipant(TeamParticipant $teamParticipant): void
    {
        if (!$teamParticipant->belongsToTeam($this->team)) {
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

    public function executeTaskAsMemberOfTeamParticipantMeetingInitiator(
            TeamParticipant $teamParticipant, ParticipantAttendee $participantAttendee,
            ITaskExecutableByMeetingInitiator $task): void
    {
        $this->assertActive();
        $teamParticipant->assertBelongsToTeam($this->team);
        $teamParticipant->executeTaskAsMeetingInitiator($participantAttendee, $task);
    }

}

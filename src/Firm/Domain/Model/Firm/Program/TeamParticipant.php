<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingData;
use Firm\Domain\Model\Firm\Program\Mission\MissionComment;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;
use Firm\Domain\Model\Firm\Program\Participant\ParticipantAttendee;
use Firm\Domain\Model\Firm\Team;
use Resources\Exception\RegularException;

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
     * @var Team
     */
    protected $team;

    public function getId(): string
    {
        return $this->id;
    }

    public function __construct(Participant $participant, string $id, Team $team)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->team = $team;
    }

    public function assertBelongsToTeam(Team $team): void
    {
        if ($this->team !== $team) {
            throw RegularException::forbidden("forbidden: can only access owned asset");
        }
    }

    public function correspondWithRegistrant(Registrant $registrant): bool
    {
        return $registrant->correspondWithTeam($this->team);
    }

    public function belongsToTeam(Team $team): bool
    {
        return $this->team === $team;
    }

    public function initiateMeeting(string $meetingId, ActivityType $meetingType, MeetingData $meetingData): Meeting
    {
        return $this->participant->initiateMeeting($meetingId, $meetingType, $meetingData);
    }

    public function submitCommentInMission(
            Mission $mission, string $missionCommentId, MissionCommentData $missionCommentData, Client $client): MissionComment
    {
        $this->participant->assertActive();
        $this->participant->assertAssetAccessible($mission);
        $missionCommentData->addRolePath('participant', $this->id);
        return $client->submitCommentInMission($mission, $missionCommentId, $missionCommentData);
    }

    public function replyMissionComment(
            MissionComment $missionComment, string $replyId, MissionCommentData $missionCommentData, Client $client): MissionComment
    {
        $this->participant->assertActive();
        $this->participant->assertAssetAccessible($missionComment);
        $missionCommentData->addRolePath('participant', $this->id);
        return $client->replyMissionComment($missionComment, $replyId, $missionCommentData);
    }

    public function executeTaskAsMeetingInitiator(
            ParticipantAttendee $participantAttendee, ITaskExecutableByMeetingInitiator $task): void
    {
        $participantAttendee->assertBelongsToParticipant($this->participant);
        $participantAttendee->executeTaskAsMeetingInitiator($task);
    }

    public function correspondWithProgram(Program $program): bool
    {
        return $this->participant->correspondWithProgram($program);
    }

    public function enable(): void
    {
        $this->participant->reenroll();
    }
    
    public function isActiveParticipantInProgram(Program $program): bool
    {
        return $this->participant->isActiveParticipantInProgram($program);
    }

}

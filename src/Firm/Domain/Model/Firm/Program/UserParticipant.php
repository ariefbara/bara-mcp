<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Program\MeetingType\Meeting;
use Firm\Domain\Model\Firm\Program\MeetingType\MeetingData;
use Firm\Domain\Model\Firm\Program\Mission\MissionComment;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;
use Firm\Domain\Model\User;

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
     * @var User
     */
    protected $user;
    
    public function __construct(Participant $participant, string $id, User $user)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->user = $user;
    }
    
    public function correspondWithRegistrant(Registrant $registrant): bool
    {
        return $registrant->correspondWithUser($this->user);
    }
    
    public function initiateMeeting(string $meetingId, ActivityType $meetingType, MeetingData $meetingData): Meeting
    {
        return $this->participant->initiateMeeting($meetingId, $meetingType, $meetingData);
    }
    
    public function submitCommentInMission(
            Mission $mission, string $missionCommentId, MissionCommentData $missionCommentData): MissionComment
    {
        $this->participant->assertActive();
        $this->participant->assertAssetAccessible($mission);
        $missionCommentData->addRolePath('participant', $this->id);
        return $this->user->submitCommentInMission($mission, $missionCommentId, $missionCommentData);
    }
    public function replyMissionComment(
            MissionComment $missionComment, string $replyId, MissionCommentData $missionCommentData): MissionComment
    {
        $this->participant->assertActive();
        $this->participant->assertAssetAccessible($missionComment);
        $missionCommentData->addRolePath('participant', $this->id);
        return $this->user->replyMissionComment($missionComment, $replyId, $missionCommentData);
    }
    
}

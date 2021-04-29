<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Model\Firm\Program\MeetingType\Meeting;
use Firm\Domain\Model\Firm\Program\MeetingType\MeetingData;
use Firm\Domain\Model\Firm\Program\Mission\MissionComment;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;

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
     * @var Client
     */
    protected $client;
    
    public function __construct(Participant $participant, string $id, Client $client)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->client = $client;
    }
    
    public function correspondWithRegistrant(Registrant $registrant): bool
    {
        return $registrant->correspondWithClient($this->client);
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
        return $this->client->submitCommentInMission($mission, $missionCommentId, $missionCommentData);
    }
    public function replyMissionComment(
            MissionComment $missionComment, string $replyId, MissionCommentData $missionCommentData): MissionComment
    {
        $this->participant->assertActive();
        $this->participant->assertAssetAccessible($missionComment);
        $missionCommentData->addRolePath('participant', $this->id);
        return $this->client->replyMissionComment($missionComment, $replyId, $missionCommentData);
    }

}

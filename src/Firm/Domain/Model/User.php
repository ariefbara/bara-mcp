<?php

namespace Firm\Domain\Model;

use Firm\Domain\Model\Firm\Program\Mission;
use Firm\Domain\Model\Firm\Program\Mission\MissionComment;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;
use Resources\Domain\ValueObject\PersonName;

class User
{

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var PersonName
     */
    protected $personName;

    protected function __construct()
    {
        
    }
    
    public function submitCommentInMission(
            Mission $mission, string $missionCommentId, MissionCommentData $missionCommentData): MissionComment
    {
        return $mission->receiveComment(
                $missionCommentId, $missionCommentData, $this->id, $this->personName->getFullName());
    }

    public function replyMissionComment(
            MissionComment $missionComment, string $replyId, MissionCommentData $missionCommentData): MissionComment
    {
        return $missionComment->receiveReply($replyId, $missionCommentData, $this->id, $this->personName->getFullName());
    }

}

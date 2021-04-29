<?php

namespace Firm\Domain\Model\Firm\Program\Mission;

use DateTimeImmutable;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\AssetInProgram;
use Firm\Domain\Model\Firm\Program\Mission;

class MissionComment implements AssetInProgram
{

    /**
     * 
     * @var Mission
     */
    protected $mission;

    /**
     * 
     * @var string
     */
    protected $id;
    
    /**
     * 
     * @var MissionComment|null
     */
    protected $repliedComment;

    /**
     * 
     * @var message
     */
    protected $message;

    /**
     * 
     * @var array
     */
    protected $rolePaths;

    /**
     * 
     * @var string
     */
    protected $userId;

    /**
     * 
     * @var string
     */
    protected $userName;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $modifiedTime;

    public function belongsToProgram(Program $program): bool
    {
        return $this->mission->belongsToProgram($program);
    }
    
    public function __construct(
            Mission $mission, string $id, MissionCommentData $missionCommentData, string $userId, string $userName)
    {
        $this->mission = $mission;
        $this->id = $id;
        $this->message = $missionCommentData->getMessage();
        $this->rolePaths = $missionCommentData->getRolePaths();
        $this->userId = $userId;
        $this->userName = $userName;
        $this->modifiedTime = \Resources\DateTimeImmutableBuilder::buildYmdHisAccuracy();
    }
    
    public function receiveReply(
            string $replyId, MissionCommentData $missionCommentData, string $userId, string $userName): MissionComment
    {
        $reply = new static($this->mission, $replyId, $missionCommentData, $userId, $userName);
        $reply->repliedComment = $this;
        return $reply;
    }


}

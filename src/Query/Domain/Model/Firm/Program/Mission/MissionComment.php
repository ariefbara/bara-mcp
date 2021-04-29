<?php

namespace Query\Domain\Model\Firm\Program\Mission;

use DateTimeImmutable;
use Query\Domain\Model\Firm\Program\Mission;

class MissionComment
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
     * @var string
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

    public function getMission(): Mission
    {
        return $this->mission;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getRepliedComment(): ?MissionComment
    {
        return $this->repliedComment;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    protected function __construct()
    {
        
    }

    public function getRolePathsJson(): string
    {
        return json_encode($this->rolePaths);
    }

    public function getModifiedTimeString(): string
    {
        return $this->modifiedTime->format('Y-m-d H:i:s');
    }

}

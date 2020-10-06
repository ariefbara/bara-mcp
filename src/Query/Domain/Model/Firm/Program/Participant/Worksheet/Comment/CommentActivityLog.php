<?php

namespace Query\Domain\Model\Firm\Program\Participant\Worksheet\Comment;

use Query\Domain\ {
    Model\Firm\Program\Participant\Worksheet\Comment,
    SharedModel\ActivityLog
};

class CommentActivityLog
{

    /**
     *
     * @var Comment
     */
    protected $comment;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var ActivityLog
     */
    protected $activityLog;

    public function getComment(): Comment
    {
        return $this->comment;
    }

    public function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        
    }

    public function getMessage(): string
    {
        return $this->activityLog->getMessage();
    }

    public function getOccuredTimeString(): string
    {
        return $this->activityLog->getOccuredTimeString();
    }

}

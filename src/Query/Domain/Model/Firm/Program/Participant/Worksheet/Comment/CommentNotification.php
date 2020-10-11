<?php

namespace Query\Domain\Model\Firm\Program\Participant\Worksheet\Comment;

use Query\Domain\{
    Model\Firm\Program\Participant\Worksheet\Comment,
    SharedModel\Notification
};

class CommentNotification
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
     * @var Notification
     */
    protected $notification;

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
        return $this->notification->getMessage();
    }

}

<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet\Comment;

use Tests\Controllers\RecordPreparation\{
    Firm\Program\Participant\Worksheet\RecordOfComment,
    Record,
    Shared\RecordOfNotification
};

class RecordOfCommentNotification implements Record
{

    /**
     *
     * @var RecordOfComment
     */
    public $comment;

    /**
     *
     * @var RecordOfNotification
     */
    public $notification;
    public $id;

    public function __construct(RecordOfComment $comment, RecordOfNotification $notification)
    {
        $this->comment = $comment;
        $this->notification = $notification;
        $this->id = $notification->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Comment_id" => $this->comment->id,
            "Notification_id" => $this->notification->id,
            "id" => $this->id,
        ];
    }

}

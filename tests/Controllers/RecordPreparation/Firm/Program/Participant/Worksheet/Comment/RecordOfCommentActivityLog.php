<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet\Comment;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\Participant\Worksheet\RecordOfComment,
    Record,
    Shared\RecordOfActivityLog
};

class RecordOfCommentActivityLog implements Record
{
    /**
     *
     * @var RecordOfComment
     */
    public $comment;
    /**
     *
     * @var RecordOfActivityLog
     */
    public $activityLog;
    /**
     *
     * @var string
     */
    public $id;
    
    public function __construct(RecordOfComment $comment, RecordOfActivityLog $activityLog)
    {
        $this->comment = $comment;
        $this->activityLog = $activityLog;
        $this->id = $activityLog->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Comment_id" => $this->comment->id,
            "ActivityLog_id" => $this->activityLog->id,
            "id" => $this->id,
        ];
    }

}

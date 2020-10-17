<?php

namespace Personnel\Domain\Model\Firm\Program\Participant\Worksheet\Comment;

use Personnel\Domain\ {
    Model\Firm\Personnel\ProgramConsultant,
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

    public function __construct(Comment $comment, string $id, string $message, ProgramConsultant $consultant)
    {
        $this->comment = $comment;
        $this->id = $id;
        $this->activityLog = new ActivityLog($id, $message, $consultant);
    }

}

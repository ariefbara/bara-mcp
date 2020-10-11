<?php

namespace Participant\Domain\Model\Participant\Worksheet\Comment;

use Participant\Domain\ {
    DependencyModel\Firm\Client\TeamMembership,
    Model\Participant\Worksheet\Comment,
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

    public function __construct(Comment $comment, string $id, string $message, ?TeamMembership $teamMember)
    {
        $this->comment = $comment;
        $this->id = $id;
        $this->activityLog = new ActivityLog($id, $message, $teamMember);
    }

}

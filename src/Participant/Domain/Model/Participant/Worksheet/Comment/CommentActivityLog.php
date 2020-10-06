<?php

namespace Participant\Domain\Model\Participant\Worksheet\Comment;

use Participant\Domain\{
    DependencyModel\Firm\Client\TeamMembership,
    Model\Participant\Worksheet\Comment,
    SharedModel\ActivityLog,
    SharedModel\ContainActvityLog
};

class CommentActivityLog implements ContainActvityLog
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

    public function __construct(Comment $comment, string $id, string $message)
    {
        $this->comment = $comment;
        $this->id = $id;
        $this->activityLog = new ActivityLog($id, $message);
    }

    public function setOperator(TeamMembership $teamMember): void
    {
        $this->activityLog->setOperator($teamMember);
    }

}

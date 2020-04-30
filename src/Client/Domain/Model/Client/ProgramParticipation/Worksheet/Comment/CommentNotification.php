<?php

namespace Client\Domain\Model\Client\ProgramParticipation\Worksheet\Comment;

use Client\Domain\Model\Client\{
    ClientNotification,
    ProgramParticipation\Worksheet\Comment
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
     * @var ClientNotification
     */
    protected $clientNotification;

    function __construct(Comment $comment, string $id, ClientNotification $clientNotification)
    {
        $this->comment = $comment;
        $this->id = $id;
        $this->clientNotification = $clientNotification;
    }

}

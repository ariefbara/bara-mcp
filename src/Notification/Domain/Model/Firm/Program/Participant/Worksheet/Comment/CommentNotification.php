<?php

namespace Notification\Domain\Model\Firm\Program\Participant\Worksheet\Comment;

use Notification\Domain\ {
    Model\Firm\Client,
    Model\Firm\Personnel,
    Model\Firm\Program\Participant\Worksheet\Comment,
    Model\User,
    SharedModel\Notification,
    SharedModel\ContainNotification
};

class CommentNotification implements ContainNotification
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

    public function __construct(Comment $comment, string $id, string $message)
    {
        $this->comment = $comment;
        $this->id = $id;
        $this->notification = new Notification($id, $message);
    }

    public function addClientRecipient(Client $client): void
    {
        $this->notification->addClientRecipient($client);
    }

    public function addPersonnelRecipient(Personnel $personnel): void
    {
        $this->notification->addPersonnelRecipient($personnel);
    }

    public function addUserRecipient(User $user): void
    {
        $this->notification->addUserRecipient($user);
    }

}

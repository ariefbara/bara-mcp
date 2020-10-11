<?php

namespace Notification\Domain\Model\Firm\Program\Participant\Worksheet\Comment;

use Notification\Domain\{
    Model\Firm\Program\Participant\Worksheet\Comment,
    SharedModel\Mail
};

class CommentMail
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
     * @var Mail
     */
    protected $mail;

    public function __construct(
            Comment $comment, string $id, string $senderMailAddress, string $senderName, string $subject,
            string $message, ?string $htmlMessage, string $recipientMailAddress, string $recipientName)
    {
        $this->comment = $comment;
        $this->id = $id;
        $this->mail = new Mail($id, $senderMailAddress, $senderName, $subject, $message, $htmlMessage,
                $recipientMailAddress, $recipientName);
    }

}

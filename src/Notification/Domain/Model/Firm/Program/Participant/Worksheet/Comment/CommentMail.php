<?php

namespace Notification\Domain\Model\Firm\Program\Participant\Worksheet\Comment;

use Notification\Domain\{
    Model\Firm\Program\Participant\Worksheet\Comment,
    SharedModel\Mail
};
use SharedContext\Domain\ValueObject\MailMessage;

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

    function __construct(
            Comment $comment, string $id, string $senderMailAddress, string $senderName,
            MailMessage $mailMessage, string $recipientMailAddress, string $recipientName)
    {
        $this->comment = $comment;
        $this->id = $id;
        $this->mail = new Mail($id, $senderMailAddress, $senderName, $mailMessage, $recipientMailAddress, $recipientName);
    }

}

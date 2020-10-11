<?php

namespace Query\Domain\Model\Firm\Program\Participant\Worksheet\Comment;

use Query\Domain\{
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

    public function getComment(): Comment
    {
        return $this->comment;
    }

    public function getId(): string
    {
        return $this->id;
    }

    function __construct()
    {
        
    }

    public function getSenderMailAddress(): string
    {
        return $this->mail->getSenderMailAddress();
    }

    public function getSenderName(): string
    {
        return $this->mail->getSenderName();
    }

    public function getSubject(): string
    {
        return $this->mail->getSubject();
    }

    public function getMessage(): string
    {
        return $this->mail->getMessage();
    }

    public function getHtmlMessage(): ?string
    {
        return $this->mail->getHtmlMessage();
    }

}

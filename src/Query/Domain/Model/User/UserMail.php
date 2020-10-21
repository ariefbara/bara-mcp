<?php

namespace Query\Domain\Model\User;

use Query\Domain\{
    Model\User,
    SharedModel\Mail
};

class UserMail
{

    /**
     *
     * @var User
     */
    protected $user;

    /**
     *
     * @var id
     */
    protected $id;

    /**
     *
     * @var Mail
     */
    protected $mail;

    public function getUser(): User
    {
        return $this->user;
    }

    public function getId(): id
    {
        return $this->id;
    }

    protected function __construct()
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

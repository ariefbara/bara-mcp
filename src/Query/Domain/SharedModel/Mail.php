<?php

namespace Query\Domain\SharedModel;

use Query\Domain\SharedModel\Mail\IcalAttachment;
use SharedContext\Domain\ValueObject\MailMessage;

class Mail
{

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $senderMailAddress;

    /**
     *
     * @var string
     */
    protected $senderName;

    /**
     *
     * @var MailMessage
     */
    protected $message;

    /**
     * 
     * @var IcalAttachment|null
     */
    protected $icalAttachment;

    public function getId(): string
    {
        return $this->id;
    }

    public function getSenderMailAddress(): string
    {
        return $this->senderMailAddress;
    }

    public function getSenderName(): string
    {
        return $this->senderName;
    }

    public function getIcalAttachment(): ?IcalAttachment
    {
        return $this->icalAttachment;
    }

    protected function __construct()
    {
        
    }

    public function getSubject(): string
    {
        return $this->message->getSubject();
    }

    function getGreetings(): string
    {
        return $this->message->getGreetings();
    }

    function getMainMessage(): string
    {
        return $this->message->getMainMessage();
    }

    function getShortcutLink(): string
    {
        return $this->message->getShortcutLink();
    }

}

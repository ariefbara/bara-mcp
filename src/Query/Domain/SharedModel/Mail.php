<?php

namespace Query\Domain\SharedModel;

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
     * @var string
     */
    protected $subject;

    /**
     *
     * @var string
     */
    protected $message;

    /**
     *
     * @var string|null
     */
    protected $htmlMessage;

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

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getHtmlMessage(): ?string
    {
        return $this->htmlMessage;
    }

    protected function __construct()
    {
        ;
    }

}

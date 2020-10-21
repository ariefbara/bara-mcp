<?php

namespace Query\Domain\Model\Firm\Client;

use Query\Domain\{
    Model\Firm\Client,
    SharedModel\Mail
};

class ClientMail
{

    /**
     *
     * @var Client
     */
    protected $client;

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

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function __construct()
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

<?php

namespace Notification\Domain\SharedModel\Mail;

use Notification\Domain\SharedModel\Mail;

class IcalAttachment
{

    /**
     * 
     * @var Mail
     */
    protected $mail;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var string
     */
    protected $content;

    public function getContent(): string
    {
        return $this->content;
    }

    public function __construct(Mail $mail, string $id, string $content)
    {
        $this->mail = $mail;
        $this->id = $id;
        $this->content = $content;
    }

}

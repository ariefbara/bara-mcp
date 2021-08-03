<?php

namespace Query\Domain\SharedModel\Mail;

use Query\Domain\SharedModel\Mail;

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

    public function getMail(): Mail
    {
        return $this->mail;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    protected function __construct()
    {
        
    }

}

<?php

namespace Query\Domain\Model\Firm\Program\MeetingType\Meeting;

use Query\Domain\Model\Firm\Program\Activity;
use Query\Domain\SharedModel\Mail;

class MeetingMail
{

    /**
     * 
     * @var Activity
     */
    protected $meeting;

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

    protected function __construct()
    {
        
    }

}

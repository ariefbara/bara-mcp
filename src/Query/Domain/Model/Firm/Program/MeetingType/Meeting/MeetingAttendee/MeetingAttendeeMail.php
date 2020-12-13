<?php

namespace Query\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee;

use Query\Domain\Model\Firm\Program\Activity\Invitee;
use Query\Domain\SharedModel\Mail;

class MeetingAttendeeMail
{
    /**
     * 
     * @var Invitee
     */
    protected $meetingAttendee;

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

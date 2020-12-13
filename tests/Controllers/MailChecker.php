<?php

namespace Tests\Controllers;

use Illuminate\Database\Connection;
use Laravel\Lumen\Routing\ProvidesConvenienceMethods;

class MailChecker extends ControllerTestCase
{
    
    public function __construct()
    {
        parent::setUp();
    }
    
    public function checkMailExist(string $subject, string $recipientEmail): self
    {
        $mailEntry = [
            "subject" => $subject,
        ];
        $this->seeInDatabase("Mail", $mailEntry);
        
        $mailRecipientEntry = [
            "recipientMailAddress" => $recipientEmail,
        ];
        $this->seeInDatabase("MailRecipient", $mailRecipientEntry);
        
        return $this;
    }
}

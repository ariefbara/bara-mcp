<?php

namespace Tests;

use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class MailSpikeTest extends TestBase
{
    protected function setUp(): void
    {
        parent::setUp();
    }
    
    public function test_sendMailInnovid()
    {
        $message = (new Swift_Message())
            ->setSubject("test mail")
            ->setFrom("noreply@innovid.xyz", "innovid.xyz")
            ->setBody("message body")
            ->setTo("purnama.adi@gmail.com", 'adi purnama');
        
        $transport = new Swift_SmtpTransport("mail.innovid.xyz", 465, "ssl");
        $transport->setUsername("noreply@innovid.xyz");
        $transport->setPassword("pnNA@TL^D+Dr");
        
        $vendor = new Swift_Mailer($transport);
        $result = $vendor->send($message);
        $this->assertEquals(1, $result);
    }
    
    public function test_sendMailQwords()
    {
        $message = (new Swift_Message())
            ->setSubject("test mail")
            ->setFrom("noreply@start.mikti.id", "start.mikti.id")
            ->setBody("message body")
            ->setTo("adi@barapraja.com", 'adi bara');
        
        $transport = new Swift_SmtpTransport("start.mikti.id", 465, "ssl");
        $transport->setUsername("noreply@start.mikti.id");
        $transport->setPassword("pr4jaB1bar4@bdg");
        
        $vendor = new Swift_Mailer($transport);
        $result = $vendor->send($message);
        $this->assertEquals(1, $result);
    }
}

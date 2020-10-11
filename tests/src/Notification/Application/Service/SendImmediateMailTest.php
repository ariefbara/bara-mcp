<?php

namespace Notification\Application\Service;

use Notification\Domain\SharedModel\Mail\Recipient;
use Tests\TestBase;

class SendImmediateMailTest extends TestBase
{
    protected $recipientRepository, $recipient;
    protected $mailSender;
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->recipient = $this->buildMockOfClass(Recipient::class);
        $this->recipientRepository = $this->buildMockOfInterface(RecipientRepository::class);
        $this->recipientRepository->expects($this->any())
                ->method("allRecipientsWithZeroAttempt")
                ->willReturn([$this->recipient]);
        
        $this->mailSender = $this->buildMockOfInterface(MailSender::class);
        
        $this->service = new SendImmediateMail($this->recipientRepository, $this->mailSender);
    }
    
    protected function execute()
    {
        $this->service->execute();
    }
    public function test_execute_setRecipientToMail()
    {
        $this->mailSender->expects($this->once())
                ->method("send")
                ->with($this->recipient);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->recipientRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}

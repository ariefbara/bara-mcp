<?php
namespace Resources\Application\Service;

use Tests\TestBase;
use Resources\Domain\Model\Mail\AccountData;
use Resources\Domain\Model\MailData;

class SendMailTest extends TestBase
{
    protected $service;
    protected $mailer, $senderAddress = 'sender@email.org', $senderName = 'sender name';
    
    protected function setUp(): void {
        $this->mailer = $this->buildMockOfInterface('\Resources\Application\Service\Mailer');
        $this->service = new SendMail($this->mailer, $this->senderName, $this->senderAddress);
    }
    
    private function executeConstruct()
    {
        return new SendMail($this->mailer, $this->senderName, $this->senderAddress);
    }
    public function test_construct_emptySenderName_throwEx()
    {
        $this->senderName = '';
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: to send mail, sender name is required';
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_construct_invalidSenderAddressFormat_throwEx()
    {
        $this->senderAddress = 'invalid address';
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: to send mail, sender address is required and must be a valid email address';
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    
    public function test_execute_sendMailThrougMailer()
    {
        $mail = $this->buildMockOfClass('\Resources\Domain\Model\Mail');
        $this->mailer->expects($this->once())
            ->method('send')
            ->with($mail);
        $this->service->execute($mail);
    }
    
}


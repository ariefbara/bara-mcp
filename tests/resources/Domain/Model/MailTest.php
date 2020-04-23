<?php
namespace Resources\Domain\Model;

use Tests\TestBase;
use Resources\Domain\Model\Mail\AccountData;


class MailTest extends TestBase
{
    protected $mail;
    protected $recipient;
    protected $dynamicAttachment;
    protected $subject = 'mail subject', $body = 'mail body', $alternativeBody = 'alternative body';
    
    protected function setUp(): void {
        parent::setUp();
        
        $this->recipient = $this->buildMockOfClass('Resources\Domain\Model\Mail\Recipient');
        $this->dynamicAttachment = $this->buildMockOfClass('\Resources\Domain\Model\Mail\DynamicAttachment');
        
        $this->mail = new TestableMail('subject', 'body', 'alternative body', $this->recipient);
    }
    
    private function executeConstruct() {
        return new TestableMail($this->subject, $this->body, $this->alternativeBody, $this->recipient);
    }
    
    function test_construct_setProperties() {
        $mail = $this->executeConstruct();
        $this->assertEquals($this->subject, $mail->subject);
        $this->assertEquals($this->body, $mail->body);
        $this->assertEquals($this->alternativeBody, $mail->alternativeBody);
        $this->assertEquals($this->recipient, $mail->recipients->first());
    }
    function test_construct_emtpySubject_throwEx() {
        $this->subject = '';
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: mail subject is required";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    function test_construct_emptyBody_throwEx() {
        $this->body = '';
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: mail body is required";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    
    public function test_addRecipient_addRecipientToCollection()
    {
        $this->mail->addRecipient($this->recipient);
        $this->assertEquals(2, $this->mail->recipients->count());
        $this->assertEquals($this->recipient, $this->mail->recipients->last());
    }
    public function test_addDynamicAttachment_addDynamicAttachmentToCollection()
    {
        $this->mail->addDynamicAttachment($this->dynamicAttachment);
        $this->assertEquals(1, $this->mail->dynamicAttachments->count());
        $this->assertEquals($this->dynamicAttachment, $this->mail->dynamicAttachments->last());
    }
}

class TestableMail extends Mail{
    public $subject, $body, $alternativeBody, $recipients, $dynamicAttachments;
}


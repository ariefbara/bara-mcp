<?php

namespace Notification\Domain\SharedModel\Mail;

use Notification\Domain\SharedModel\Mail;
use Tests\TestBase;

class RecipientTest extends TestBase
{
    protected $mail;
    protected $recipient;
    protected $id = "newId";
    protected $recipientMailAddress = "new_recipient@mail.org";
    protected $recipientName = "new recipient name";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->mail = $this->buildMockOfClass(Mail::class);
        $this->recipient = new TestableRecipient($this->mail, "id", "recipient@email.org", "recipient name");
    }
    
    public function test_construct_setProperties()
    {
        $recipient = new TestableRecipient($this->mail, $this->id, $this->recipientMailAddress, $this->recipientName);
        $this->assertEquals($this->mail, $recipient->mail);
        $this->assertEquals($this->id, $recipient->id);
        $this->assertEquals($this->recipientMailAddress, $recipient->recipientMailAddress);
        $this->assertEquals($this->recipientName, $recipient->recipientName);
        $this->assertEquals(0, $recipient->attempt);
        $this->assertFalse($recipient->sent);
    }
    
    public function test_sendSuccessful_setSentTrue()
    {
        $this->recipient->sendSuccessful();
        $this->assertTrue($this->recipient->sent);
    }
    
    public function test_increateAttempt_increateAttemptValue()
    {
        $this->recipient->increaseAttempt();
        $this->assertEquals(1, $this->recipient->attempt);
    }
}

class TestableRecipient extends Recipient
{
    public $mail;
    public $id;
    public $recipientMailAddress;
    public $recipientName;
    public $sent;
    public $attempt;
}

<?php

namespace Notification\Domain\SharedModel\Mail;

use Tests\TestBase;

class RecipientTest extends TestBase
{
    protected $recipientMailAddress = "new_recipient@mail.org";
    protected $recipientName = "new recipient name";
    
    protected function setUp(): void
    {
        parent::setUp();
    }
    
    public function test_construct_setProperties()
    {
        $recipient = new TestableRecipient($this->recipientMailAddress, $this->recipientName);
        $this->assertEquals($this->recipientMailAddress, $recipient->recipientMailAddress);
        $this->assertEquals($this->recipientName, $recipient->recipientName);
        $this->assertEquals(0, $recipient->attempt);
        $this->assertFalse($recipient->sent);
        
    }
}

class TestableRecipient extends Recipient
{
    public $recipientMailAddress;
    public $recipientName;
    public $sent;
    public $attempt;
}

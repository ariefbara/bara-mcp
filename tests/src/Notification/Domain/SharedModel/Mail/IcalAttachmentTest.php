<?php

namespace Notification\Domain\SharedModel\Mail;

use Tests\TestBase;

class IcalAttachmentTest extends TestBase
{
    protected $mail;
    protected $icalAttachment;
    protected $id = 'new-id';
    protected $content = 'new content';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->mail = $this->buildMockOfClass(\Notification\Domain\SharedModel\Mail::class);
        $this->icalAttachment = new TestableIcalAttachment($this->mail, 'id', 'content');
    }
    
    public function test_construct_setProperties()
    {
        $icalAttachment = new TestableIcalAttachment($this->mail, $this->id, $this->content);
        $this->assertEquals($this->mail, $icalAttachment->mail);
        $this->assertEquals($this->id, $icalAttachment->id);
        $this->assertEquals($this->content, $icalAttachment->content);
    }
}

class TestableIcalAttachment extends IcalAttachment
{
    public $mail;
    public $id;
    public $content;
}

<?php

namespace Notification\Domain\Model\Firm\Program\Participant\Worksheet\Comment;

use Notification\Domain\ {
    Model\Firm\Program\Participant\Worksheet\Comment,
    SharedModel\Mail
};
use Tests\TestBase;

class CommentMailTest extends TestBase
{

    protected $comment;
    protected $id = "newId", $senderMailAddress = "new_sender@email.org", $senderName = "new sender name",
            $subject = "new subject", $message = "new message", $htmlMessage = "new html message",
            $recipientMailAddress = "new_recipient@email.org", $recipientName = "new recipient name";

    protected function setUp(): void
    {
        parent::setUp();
        $this->comment = $this->buildMockOfClass(Comment::class);
    }

    public function test_construct_setProperties()
    {
        $commentMail = new TestableCommentMail(
                $this->comment, $this->id, $this->senderMailAddress, $this->senderName, $this->subject, $this->message,
                $this->htmlMessage, $this->recipientMailAddress, $this->recipientName);
        
        $this->assertEquals($this->comment, $commentMail->comment);
        $this->assertEquals($this->id, $commentMail->id);
        $this->assertInstanceOf(Mail::class, $commentMail->mail);
    }

}

class TestableCommentMail extends CommentMail
{

    public $comment;
    public $id;
    public $mail;

}

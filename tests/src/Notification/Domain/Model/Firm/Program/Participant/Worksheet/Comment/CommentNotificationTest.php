<?php

namespace Notification\Domain\Model\Firm\Program\Participant\Worksheet\Comment;

use Notification\Domain\ {
    Model\Firm\Client,
    Model\Firm\Personnel,
    Model\Firm\Program\Participant\Worksheet\Comment,
    Model\User,
    SharedModel\Notification
};
use Tests\TestBase;

class CommentNotificationTest extends TestBase
{
    protected $comment;
    protected $notification;
    protected $commentNotification;
    protected $id = "newId", $message = "new message";
    
    protected $user;
    protected $client;
    protected $personnel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->comment = $this->buildMockOfClass(Comment::class);
        $this->commentNotification = new TestableCommentNotification($this->comment, "id", "message");
        
        $this->notification = $this->buildMockOfClass(Notification::class);
        $this->commentNotification->notification = $this->notification;
        
        $this->user = $this->buildMockOfClass(User::class);
        $this->client = $this->buildMockOfClass(Client::class);
        $this->personnel = $this->buildMockOfClass(Personnel::class);
    }
    
    public function test_construct_setProperties()
    {
        $commentNotification = new TestableCommentNotification($this->comment, $this->id, $this->message);
        $this->assertEquals($this->comment, $commentNotification->comment);
        $this->assertEquals($this->id, $commentNotification->id);
        $notification = new Notification($this->id, $this->message);
        $this->assertEquals($notification, $commentNotification->notification);
    }
    public function test_addClientRecipient_executeNotificationsAddClientRecipient()
    {
        $this->notification->expects($this->once())
                ->method("addClientRecipient");
        $this->commentNotification->addClientRecipient($this->client);
    }
    public function test_addPersonnelRecipient_executeNotificationsAddPersonnelRecipient()
    {
        $this->notification->expects($this->once())
                ->method("addPersonnelRecipient");
        $this->commentNotification->addPersonnelRecipient($this->personnel);
    }
    public function test_addUserRecipient_executeNotificationsAddUserRecipient()
    {
        $this->notification->expects($this->once())
                ->method("addUserRecipient");
        $this->commentNotification->addUserRecipient($this->user);
    }
}

class TestableCommentNotification extends CommentNotification
{
    public $comment;
    public $id;
    public $notification;
}

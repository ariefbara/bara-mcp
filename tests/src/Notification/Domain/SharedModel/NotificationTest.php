<?php

namespace Notification\Domain\SharedModel;

use Notification\Domain\ {
    Model\Firm\Client,
    Model\Firm\Personnel,
    Model\User,
    SharedModel\Notification\ClientNotificationRecipient,
    SharedModel\Notification\PersonnelNotificationRecipient,
    SharedModel\Notification\UserNotificationRecipient
};
use Tests\TestBase;

class NotificationTest extends TestBase
{
    protected $notification;
    protected $id = "newId";
    protected $message = "new message";
    
    protected $user;
    protected $client;
    protected $personnel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notification = new TestableNotification("id", "message");
        
        $this->user = $this->buildMockOfClass(User::class);
        $this->client = $this->buildMockOfClass(Client::class);
        $this->personnel = $this->buildMockOfClass(Personnel::class);
    }
    
    public function test_construct_setProperties()
    {
        $notification = new TestableNotification($this->id, $this->message);
        $this->assertEquals($this->id, $notification->id);
        $this->assertEquals($this->message, $notification->message);
    }
    
    public function test_addUserRecipient_addUserRecipientNotification()
    {
        $this->notification->addUserRecipient($this->user);
        $this->assertInstanceOf(UserNotificationRecipient::class, $this->notification->userNotificationRecipients->first());
    }
    
    public function test_addClientRecipient_addClientRecipientNotification()
    {
        $this->notification->addClientRecipient($this->client);
        $this->assertInstanceOf(ClientNotificationRecipient::class, $this->notification->ClientNotificationRecipients->first());
    }
    
    public function test_addPersonnelRecipient_addPersonnelRecipientNotification()
    {
        $this->notification->addPersonnelRecipient($this->personnel);
        $this->assertInstanceOf(PersonnelNotificationRecipient::class, $this->notification->personnelNotificationRecipients->first());
    }
}

class TestableNotification extends Notification
{
    public $id;
    public $message;
    public $userNotificationRecipients;
    public $ClientNotificationRecipients;
    public $personnelNotificationRecipients;
}

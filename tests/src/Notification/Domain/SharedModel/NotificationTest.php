<?php

namespace Notification\Domain\SharedModel;

use Notification\Domain\Model\Firm\Client;
use Notification\Domain\Model\Firm\Manager;
use Notification\Domain\Model\Firm\Personnel;
use Notification\Domain\Model\User;
use Notification\Domain\SharedModel\Notification\ClientNotificationRecipient;
use Notification\Domain\SharedModel\Notification\ManagerNotificationRecipient;
use Notification\Domain\SharedModel\Notification\PersonnelNotificationRecipient;
use Notification\Domain\SharedModel\Notification\UserNotificationRecipient;
use Tests\TestBase;

class NotificationTest extends TestBase
{
    protected $notification;
    protected $id = "newId";
    protected $message = "new message";
    
    protected $user;
    protected $client;
    protected $personnel;
    protected $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notification = new TestableNotification("id", "message");
        
        $this->user = $this->buildMockOfClass(User::class);
        $this->client = $this->buildMockOfClass(Client::class);
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->manager = $this->buildMockOfClass(Manager::class);
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
        $this->assertInstanceOf(ClientNotificationRecipient::class, $this->notification->clientNotificationRecipients->first());
    }
    
    public function test_addPersonnelRecipient_addPersonnelRecipientNotification()
    {
        $this->notification->addPersonnelRecipient($this->personnel);
        $this->assertInstanceOf(PersonnelNotificationRecipient::class, $this->notification->personnelNotificationRecipients->first());
    }
    
    public function test_addManagerRecipient_addManagerRecipientNotification()
    {
        $this->notification->addManagerRecipient($this->manager);
        $this->assertInstanceOf(ManagerNotificationRecipient::class, $this->notification->managerNotificationRecipients->first());
    }
}

class TestableNotification extends Notification
{
    public $id;
    public $message;
    public $userNotificationRecipients;
    public $clientNotificationRecipients;
    public $personnelNotificationRecipients;
    public $managerNotificationRecipients;
}

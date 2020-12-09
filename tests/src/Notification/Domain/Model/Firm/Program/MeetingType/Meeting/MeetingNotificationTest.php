<?php

namespace Notification\Domain\Model\Firm\Program\MeetingType\Meeting;

use Notification\Domain\Model\Firm\Client;
use Notification\Domain\Model\Firm\Manager;
use Notification\Domain\Model\Firm\Personnel;
use Notification\Domain\Model\Firm\Program\MeetingType\Meeting;
use Notification\Domain\Model\User;
use Notification\Domain\SharedModel\Notification;
use Tests\TestBase;

class MeetingNotificationTest extends TestBase
{
    protected $meeting;
    protected $meetingNotification;
    protected $notification;
    protected $id = "newId", $message = "new message";
    protected $client;
    protected $user;
    protected $personnel;
    protected $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->meeting = $this->buildMockOfClass(Meeting::class);
        $this->meetingNotification = new TestableMeetingNotification($this->meeting, "id", "message");
        $this->notification = $this->buildMockOfClass(Notification::class);
        $this->meetingNotification->notification = $this->notification;
        
        $this->client = $this->buildMockOfClass(Client::class);
        $this->user = $this->buildMockOfClass(User::class);
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->manager = $this->buildMockOfClass(Manager::class);
    }
    
    public function test_construct_setProperties()
    {
        $meetingNotfication = new TestableMeetingNotification($this->meeting, $this->id, $this->message);
        $this->assertEquals($this->meeting, $meetingNotfication->meeting);
        $this->assertEquals($this->id, $meetingNotfication->id);
        $notification = new Notification($this->id, $this->message);
        $this->assertEquals($notification, $meetingNotfication->notification);
    }
    
    public function test_addClientRecipient_addNotificationsClientRecipient()
    {
        $this->notification->expects($this->once())
                ->method("addClientRecipient")
                ->with($this->client);
        $this->meetingNotification->addClientRecipient($this->client);
    }
    
    public function test_addUserRecipient_addNotificationsUserRecipient()
    {
        $this->notification->expects($this->once())
                ->method("addUserRecipient")
                ->with($this->user);
        $this->meetingNotification->addUserRecipient($this->user);
    }
    
    public function test_addPersonnelRecipient_addNotificationsPersonnelRecipient()
    {
        $this->notification->expects($this->once())
                ->method("addPersonnelRecipient")
                ->with($this->personnel);
        $this->meetingNotification->addPersonnelRecipient($this->personnel);
    }
    
    public function test_addManagerRecipient_addNotificationsManagerRecipient()
    {
        $this->notification->expects($this->once())
                ->method("addManagerRecipient")
                ->with($this->manager);
        $this->meetingNotification->addManagerRecipient($this->manager);
    }
}

class TestableMeetingNotification extends MeetingNotification
{
    public $meeting;
    public $id;
    public $notification;
}

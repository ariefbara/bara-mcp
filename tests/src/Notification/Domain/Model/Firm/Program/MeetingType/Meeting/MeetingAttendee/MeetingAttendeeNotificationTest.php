<?php

namespace Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee;

use Notification\Domain\Model\Firm\Client;
use Notification\Domain\Model\Firm\Manager;
use Notification\Domain\Model\Firm\Personnel;
use Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee;
use Notification\Domain\Model\User;
use Notification\Domain\SharedModel\Notification;
use Tests\TestBase;

class MeetingAttendeeNotificationTest extends TestBase
{
    protected $meetingAttendee;
    protected $meetingAttendeeNotification;
    protected $notification;
    protected $id = "newId", $message = "new message";
    protected $client;
    protected $user;
    protected $personnel;
    protected $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->meetingAttendee = $this->buildMockOfClass(MeetingAttendee::class);
        $this->meetingAttendeeNotification = new TestableMeetingAttendeeNotification($this->meetingAttendee, "id", "message");
        $this->notification = $this->buildMockOfClass(Notification::class);
        $this->meetingAttendeeNotification->notification = $this->notification;
        
        $this->client = $this->buildMockOfClass(Client::class);
        $this->user = $this->buildMockOfClass(User::class);
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->manager = $this->buildMockOfClass(Manager::class);
    }
    
    public function test_construct_setProperties()
    {
        $meetingAttendeeNotfication = new TestableMeetingAttendeeNotification($this->meetingAttendee, $this->id, $this->message);
        $this->assertEquals($this->meetingAttendee, $meetingAttendeeNotfication->meetingAttendee);
        $this->assertEquals($this->id, $meetingAttendeeNotfication->id);
        $notification = new Notification($this->id, $this->message);
        $this->assertEquals($notification, $meetingAttendeeNotfication->notification);
    }
    
    public function test_addClientRecipient_addNotificationsClientRecipient()
    {
        $this->notification->expects($this->once())
                ->method("addClientRecipient")
                ->with($this->client);
        $this->meetingAttendeeNotification->addClientRecipient($this->client);
    }
    
    public function test_addUserRecipient_addNotificationsUserRecipient()
    {
        $this->notification->expects($this->once())
                ->method("addUserRecipient")
                ->with($this->user);
        $this->meetingAttendeeNotification->addUserRecipient($this->user);
    }
    
    public function test_addPersonnelRecipient_addNotificationsPersonnelRecipient()
    {
        $this->notification->expects($this->once())
                ->method("addPersonnelRecipient")
                ->with($this->personnel);
        $this->meetingAttendeeNotification->addPersonnelRecipient($this->personnel);
    }
    
    public function test_addManagerRecipient_addNotificationsManagerRecipient()
    {
        $this->notification->expects($this->once())
                ->method("addManagerRecipient")
                ->with($this->manager);
        $this->meetingAttendeeNotification->addManagerRecipient($this->manager);
    }
}

class TestableMeetingAttendeeNotification extends MeetingAttendeeNotification
{
    public $meetingAttendee;
    public $id;
    public $notification;
}

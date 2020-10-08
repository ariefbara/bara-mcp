<?php

namespace Notification\Domain\SharedModel\Notification;

use Notification\Domain\ {
    Model\User,
    SharedModel\Notification
};
use Resources\DateTimeImmutableBuilder;
use Tests\TestBase;

class UserNotificationRecipientTest extends TestBase
{
    protected $notification;
    protected $user;
    protected $id = "newId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->notification = $this->buildMockOfClass(Notification::class);
        $this->user = $this->buildMockOfClass(User::class);
    }
    
    public function test_construct_setProperties()
    {
        $userNotificationRecipient = new TestableUserNotificationRecipient($this->notification, $this->id, $this->user);
        $this->assertEquals($this->notification, $userNotificationRecipient->notification);
        $this->assertEquals($this->id, $userNotificationRecipient->id);
        $this->assertEquals($this->user, $userNotificationRecipient->user);
        $this->assertFalse($userNotificationRecipient->read);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $userNotificationRecipient->notifiedTime);
    }
}

class TestableUserNotificationRecipient extends UserNotificationRecipient
{

    public $notification;
    public $id;
    public $user;
    public $read;
    public $notifiedTime;

}

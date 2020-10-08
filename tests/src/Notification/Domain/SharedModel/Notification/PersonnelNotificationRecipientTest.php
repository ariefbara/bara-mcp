<?php

namespace Notification\Domain\SharedModel\Notification;

use Notification\Domain\ {
    Model\Firm\Personnel,
    SharedModel\Notification
};
use Resources\DateTimeImmutableBuilder;
use Tests\TestBase;

class PersonnelNotificationRecipientTest extends TestBase
{
    protected $notification;
    protected $personnel;
    protected $id = "newId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->notification = $this->buildMockOfClass(Notification::class);
        $this->personnel = $this->buildMockOfClass(Personnel::class);
    }
    
    public function test_construct_setProperties()
    {
        $personnelNotificationRecipient = new TestablePersonnelNotificationRecipient($this->notification, $this->id, $this->personnel);
        $this->assertEquals($this->notification, $personnelNotificationRecipient->notification);
        $this->assertEquals($this->id, $personnelNotificationRecipient->id);
        $this->assertEquals($this->personnel, $personnelNotificationRecipient->personnel);
        $this->assertFalse($personnelNotificationRecipient->read);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $personnelNotificationRecipient->notifiedTime);
    }
}

class TestablePersonnelNotificationRecipient extends PersonnelNotificationRecipient
{
    public $notification;
    public $id;
    public $personnel;
    public $read;
    public $notifiedTime;
}

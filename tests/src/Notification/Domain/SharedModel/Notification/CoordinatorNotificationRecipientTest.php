<?php

namespace Notification\Domain\SharedModel\Notification;

use Notification\Domain\Model\Firm\Program\Coordinator;
use Notification\Domain\SharedModel\Notification;
use Tests\TestBase;

class CoordinatorNotificationRecipientTest extends TestBase
{
    protected $notification;
    protected $coordinator;
    protected $id = "newId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->notification = $this->buildMockOfClass(Notification::class);
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
    }
    
    public function test_construct_setProperties()
    {
        $coordinatorNotificationRecipient = new TestableCoordinatorNotificationRecipient(
                $this->notification, $this->id, $this->coordinator);
        
        $this->assertEquals($this->notification, $coordinatorNotificationRecipient->notification);
        $this->assertEquals($this->id, $coordinatorNotificationRecipient->id);
        $this->assertEquals($this->coordinator, $coordinatorNotificationRecipient->coordinator);
        $this->assertFalse($coordinatorNotificationRecipient->read);
        $this->assertEquals(\Resources\DateTimeImmutableBuilder::buildYmdHisAccuracy(), $coordinatorNotificationRecipient->notifiedTime);
    }
}

class TestableCoordinatorNotificationRecipient extends CoordinatorNotificationRecipient
{
    public $notification;
    public $id;
    public $coordinator;
    public $read;
    public $notifiedTime;
}

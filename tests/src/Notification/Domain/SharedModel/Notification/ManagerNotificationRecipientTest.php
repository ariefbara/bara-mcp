<?php

namespace Notification\Domain\SharedModel\Notification;

use Notification\Domain\Model\Firm\Manager;
use Notification\Domain\SharedModel\Notification;
use Resources\DateTimeImmutableBuilder;
use Tests\TestBase;

class ManagerNotificationRecipientTest extends TestBase
{
    protected $notification;
    protected $manager;
    protected $id = "newId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->notification = $this->buildMockOfClass(Notification::class);
        $this->manager = $this->buildMockOfClass(Manager::class);
    }
    
    public function test_construct_setProperties()
    {
        $managerNotificationRecipient = new TestableManagerNotificationRecipient($this->notification, $this->id, $this->manager);
        $this->assertEquals($this->notification, $managerNotificationRecipient->notification);
        $this->assertEquals($this->id, $managerNotificationRecipient->id);
        $this->assertEquals($this->manager, $managerNotificationRecipient->manager);
        $this->assertFalse($managerNotificationRecipient->read);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $managerNotificationRecipient->notifiedTime);
    }
}

class TestableManagerNotificationRecipient extends ManagerNotificationRecipient
{
    public $notification;
    public $id;
    public $manager;
    public $read;
    public $notifiedTime;
}

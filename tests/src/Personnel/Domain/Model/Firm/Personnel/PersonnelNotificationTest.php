<?php

namespace Personnel\Domain\Model\Firm\Personnel;

use Personnel\Domain\Model\Firm\Personnel;
use Shared\Domain\Model\Notification;
use Tests\TestBase;

class PersonnelNotificationTest extends TestBase
{
    protected $personnel;
    protected $notification;
    protected $personnelNotification;
    protected $id = 'personnelNotificationId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->notification = $this->buildMockOfClass(Notification::class);
        $this->personnelNotification = new TestablePersonnelNotification($this->personnel, 'id', $this->notification);
    }
    
    public function test_construct_setProperties()
    {
        $personnelNotification = new TestablePersonnelNotification($this->personnel, $this->id, $this->notification);
        $this->assertEquals($this->personnel, $personnelNotification->personnel);
        $this->assertEquals($this->id, $personnelNotification->id);
        $this->assertEquals($this->notification, $personnelNotification->notification);
    }
    public function test_read_readNotification()
    {
        $this->notification->expects($this->once())
                ->method('read');
        $this->personnelNotification->read();
    }
}

class TestablePersonnelNotification extends PersonnelNotification
{
    public $personnel, $id, $notification;
}

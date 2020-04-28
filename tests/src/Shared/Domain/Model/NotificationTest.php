<?php

namespace Shared\Domain\Model;

use Tests\TestBase;

class NotificationTest extends TestBase
{
    protected $notification;
    protected $id = 'newId', $message = 'new message';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->notification = new TestableNotification('id', 'message');
    }
    public function test_construct_setProperties()
    {
        $notification = new TestableNotification($this->id, $this->message);
        $this->assertEquals($this->id, $notification->id);
        $this->assertEquals($this->message, $notification->message);
        $this->assertFalse($notification->read);
        $this->assertEquals($this->YmdHisStringOfCurrentTime(), $notification->notifiedTime->format('Y-m-d H:i:s'));
    }
    public function test_read_setReadFlagTrue()
    {
        $this->notification->read();
        $this->assertTrue($this->notification->read);
    }
}

class TestableNotification extends Notification{
    public $id, $message, $read, $notifiedTime;
}

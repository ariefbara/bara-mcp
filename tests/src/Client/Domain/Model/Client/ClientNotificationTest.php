<?php

namespace Client\Domain\Model\Client;

use Client\Domain\Model\Client;
use Shared\Domain\Model\Notification;
use Tests\TestBase;

class ClientNotificationTest extends TestBase
{

    protected $clientNotification;
    protected $client;
    protected $notification;
    protected $id = 'newId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->notification = $this->buildMockOfClass(Notification::class);
        $this->clientNotification = new TestableClientNotification($this->client, 'id', $this->notification);
    }
    public function test_construct_setProperties()
    {
        $clientNotification = new TestableClientNotification($this->client, $this->id, $this->notification);
        $this->assertEquals($this->client, $clientNotification->client);
        $this->assertEquals($this->id, $clientNotification->id);
        $this->assertEquals($this->notification, $clientNotification->notification);
    }
    public function test_read_readNotification()
    {
        $this->notification->expects($this->once())
                ->method('read');
        $this->clientNotification->read();
    }

}

class TestableClientNotification extends ClientNotification
{
    public $client;
    public $id;
    public $notification;
}

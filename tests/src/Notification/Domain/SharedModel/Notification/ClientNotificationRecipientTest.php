<?php

namespace Notification\Domain\SharedModel\Notification;

use Notification\Domain\ {
    Model\Firm\Client,
    SharedModel\Notification
};
use Resources\DateTimeImmutableBuilder;
use Tests\TestBase;

class ClientNotificationRecipientTest extends TestBase
{
    protected $notification;
    protected $client;
    protected $id = "newId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->notification = $this->buildMockOfClass(Notification::class);
        $this->client = $this->buildMockOfClass(Client::class);
    }
    
    public function test_construct_setProperties()
    {
        $clientNotificationRecipient = new TestableClientNotificationRecipient($this->notification, $this->id, $this->client);
        $this->assertEquals($this->notification, $clientNotificationRecipient->notification);
        $this->assertEquals($this->id, $clientNotificationRecipient->id);
        $this->assertEquals($this->client, $clientNotificationRecipient->client);
        $this->assertFalse($clientNotificationRecipient->read);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $clientNotificationRecipient->notifiedTime);
    }
}

class TestableClientNotificationRecipient extends ClientNotificationRecipient
{
    public $notification;
    public $id;
    public $client;
    public $read;
    public $notifiedTime;
}

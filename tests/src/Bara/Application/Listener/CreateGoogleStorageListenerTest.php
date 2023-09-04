<?php

namespace Bara\Application\Listener;

use Bara\Domain\Event\FirmCreatedEvent;
use Resources\Infrastructure\Persistence\Google\GoogleStorage;
use Tests\TestBase;

class CreateGoogleStorageListenerTest extends TestBase
{
    protected $googleStorage;
    protected $listener;
    protected $event, $firmId = 'firmId', $firmIdentifier = 'firmIdentifier';

    protected function setUp(): void
    {
        parent::setUp();
        $this->googleStorage = $this->buildMockOfClass(GoogleStorage::class);
        $this->listener = new CreateGoogleStorageListener($this->googleStorage);
        $this->event = new FirmCreatedEvent($this->firmId, $this->firmIdentifier);
    }
    
    //
    protected function handle(): void
    {
        $this->listener->handle($this->event);
    }
    public function test_handle_createGoogleCloudStorageBucket()
    {
        $this->googleStorage->expects($this->once())
                ->method('createBucket')
                ->with($this->firmIdentifier);
        $this->handle();
    }
}

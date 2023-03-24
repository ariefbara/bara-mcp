<?php

namespace SharedContext\Application\Listener;

use Resources\Infrastructure\Persistence\Google\GoogleStorage;
use SharedContext\Domain\Event\FileInfoCreatedEvent;
use Tests\TestBase;

class CreateSignedUploadListenerTest extends TestBase
{
    protected $googleStorage;
    protected $listener;
    //
    protected $event;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->googleStorage = $this->buildMockOfClass(GoogleStorage::class);
        $this->listener = new CreateSignedUploadListener($this->googleStorage);
        //
        $this->event = new FileInfoCreatedEvent('bucket', 'object');
    }
    
    //
    protected function handle()
    {
        $this->listener->handle($this->event);
    }
    public function test_handle_setSignedUploadUrlFromStorage()
    {
        $this->googleStorage->expects($this->once())
                ->method('createSignedUploadForObjectInBucket')
                ->with($this->event->getBucketName(), $this->event->getObjectName())
                ->willReturn($signedUrl = 'https://storage.google.com/asdkfjsldfjlsakdjflasdfsdfsdfl');
        $this->handle();
        $this->assertEquals($signedUrl, $this->listener->getSignedUploadUrl());
    }
}

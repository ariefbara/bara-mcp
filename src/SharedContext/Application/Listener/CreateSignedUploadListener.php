<?php

namespace SharedContext\Application\Listener;

use Resources\Application\Event\Event;
use Resources\Application\Event\Listener;
use Resources\Infrastructure\Persistence\Google\GoogleStorage;
use SharedContext\Domain\Event\FileInfoCreatedEvent;

class CreateSignedUploadListener implements Listener
{

    protected GoogleStorage $googleStorage;
    protected string $signedUploadUrl;

    public function getSignedUploadUrl(): string
    {
        return $this->signedUploadUrl;
    }

    public function __construct(GoogleStorage $googleStorage)
    {
        $this->googleStorage = $googleStorage;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(FileInfoCreatedEvent $event): void
    {
        $this->signedUploadUrl = $this->googleStorage
                ->createSignedUploadForObjectInBucket($event->getBucketName(), $event->getObjectName(), $event->getContentType());
    }

}

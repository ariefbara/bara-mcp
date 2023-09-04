<?php

namespace Bara\Application\Listener;

use Bara\Domain\Event\FirmCreatedEvent;
use Resources\Application\Event\Event;
use Resources\Application\Event\Listener;
use Resources\Infrastructure\Persistence\Google\GoogleStorage;

class CreateGoogleStorageListener implements Listener
{

    protected GoogleStorage $googlStorage;

    public function __construct(GoogleStorage $googlStorage)
    {
        $this->googlStorage = $googlStorage;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(FirmCreatedEvent $event): void
    {
        $this->googlStorage->createBucket($event->getIdentifier());
    }
}

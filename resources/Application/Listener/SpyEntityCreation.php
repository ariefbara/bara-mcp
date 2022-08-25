<?php

namespace Resources\Application\Listener;

use Resources\Application\Event\Event;
use Resources\Application\Event\Listener;
use Resources\Domain\Event\CommonEvent;

class SpyEntityCreation implements Listener
{

    /**
     * 
     * @var string|null
     */
    protected $entityId;

    public function getEntityId(): ?string
    {
        return $this->entityId;
    }

    public function __construct()
    {
        
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    public function execute(CommonEvent $event): void
    {
        $this->entityId = $event->getId();
    }

}

<?php

namespace Firm\Application\Listener;

use Resources\Application\Event\Event;
use Resources\Application\Event\Listener;
use Resources\Domain\Event\CommonEvent;

class AddClientParticipant implements Listener
{

    /**
     * 
     * @var ClientRegistrantRepository
     */
    protected $clientRegistrantRepository;

    public function __construct(ClientRegistrantRepository $clientRegistrantRepository)
    {
        $this->clientRegistrantRepository = $clientRegistrantRepository;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(CommonEvent $event): void
    {
        $clientRegistrant = $this->clientRegistrantRepository->ofRegistrantIdOrNull($event->getId());
        if ($clientRegistrant) {
            $clientRegistrant->addAsProgramParticipant();
            $this->clientRegistrantRepository->update();
        }
    }

}

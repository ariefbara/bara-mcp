<?php

namespace Firm\Application\Listener;

use Resources\Application\Event\Event;
use Resources\Application\Event\Listener;
use Resources\Domain\Event\CommonEvent;

class AddTeamParticipant implements Listener
{

    /**
     * 
     * @var TeamRegistrantRepository
     */
    protected $teamRegistrantRepository;

    public function __construct(TeamRegistrantRepository $teamRegistrantRepository)
    {
        $this->teamRegistrantRepository = $teamRegistrantRepository;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(CommonEvent $event): void
    {
        $teamRegistrant = $this->teamRegistrantRepository->ofRegistrantIdOrNull($event->getId());
        if ($teamRegistrant) {
            $teamRegistrant->addAsProgramParticipant();
            $this->teamRegistrantRepository->update();
        }
    }

}

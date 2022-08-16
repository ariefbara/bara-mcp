<?php

namespace Payment\Application\Listener;

use Resources\Application\Event\Event;
use Resources\Application\Event\Listener;
use Resources\Domain\Event\CommonEvent;
use SharedContext\Domain\Task\Dependency\PaymentGateway;

class GenerateTeamParticipantInvoice implements Listener
{

    /**
     * 
     * @var TeamParticipantRepository
     */
    protected $teamParticipantRepository;

    /**
     * 
     * @var PaymentGateway
     */
    protected $paymentGateway;

    public function __construct(TeamParticipantRepository $teamParticipantRepository, PaymentGateway $paymentGateway)
    {
        $this->teamParticipantRepository = $teamParticipantRepository;
        $this->paymentGateway = $paymentGateway;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }
    
    protected function execute(CommonEvent $event): void
    {
        $teamParticipant = $this->teamParticipantRepository->ofId($event->getId());
        if ($teamParticipant) {
            $teamParticipant->generateInvoice($this->paymentGateway);
        }
        $this->teamParticipantRepository->update();
    }

}

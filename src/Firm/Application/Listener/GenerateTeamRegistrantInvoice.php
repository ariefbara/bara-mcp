<?php

namespace Firm\Application\Listener;

use Resources\Application\Event\Event;
use Resources\Application\Event\Listener;
use Resources\Domain\Event\CommonEvent;
use SharedContext\Domain\Task\Dependency\PaymentGateway;

class GenerateTeamRegistrantInvoice implements Listener
{

    /**
     * 
     * @var TeamRegistrantRepository
     */
    protected $teamRegistrantRepository;

    /**
     * 
     * @var PaymentGateway
     */
    protected $paymentGateway;

    public function __construct(TeamRegistrantRepository $teamRegistrantRepository, PaymentGateway $paymentGateway)
    {
        $this->teamRegistrantRepository = $teamRegistrantRepository;
        $this->paymentGateway = $paymentGateway;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(CommonEvent $event): void
    {
        $teamRegistrant = $this->teamRegistrantRepository->ofRegistrantIdOrNull($event->getId());
        if ($teamRegistrant) {
            $teamRegistrant->generateInvoice($this->paymentGateway);
            $this->teamRegistrantRepository->update();
        }
    }

}

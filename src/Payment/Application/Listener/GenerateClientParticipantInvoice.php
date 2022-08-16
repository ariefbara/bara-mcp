<?php

namespace Payment\Application\Listener;

use Resources\Application\Event\Event;
use Resources\Application\Event\Listener;
use Resources\Domain\Event\CommonEvent;
use SharedContext\Domain\Task\Dependency\PaymentGateway;

class GenerateClientParticipantInvoice implements Listener
{

    /**
     * 
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;

    /**
     * 
     * @var PaymentGateway
     */
    protected $paymentGateway;

    public function __construct(
            ClientParticipantRepository $clientParticipantRepository, PaymentGateway $paymentGateway)
    {
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->paymentGateway = $paymentGateway;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    public function execute(CommonEvent $event): void
    {
        $clientParticipant = $this->clientParticipantRepository->ofId($event->getId());
        if ($clientParticipant) {
            $clientParticipant->generateInvoice($this->paymentGateway);
            $this->clientParticipantRepository->update();
        }
    }

}

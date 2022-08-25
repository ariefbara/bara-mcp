<?php

namespace Firm\Application\Listener;

use Resources\Application\Event\Event;
use Resources\Application\Event\Listener;
use Resources\Domain\Event\CommonEvent;
use SharedContext\Domain\Task\Dependency\PaymentGateway;

class GenerateClientRegistrantInvoice implements Listener
{

    /**
     * 
     * @var ClientRegistrantRepository
     */
    protected $clientRegistrantRepository;

    /**
     * 
     * @var PaymentGateway
     */
    protected $paymentGateway;

    public function __construct(ClientRegistrantRepository $clientRegistrantRepository, PaymentGateway $paymentGateway)
    {
        $this->clientRegistrantRepository = $clientRegistrantRepository;
        $this->paymentGateway = $paymentGateway;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(CommonEvent $event): void
    {
        $clientRegistrant = $this->clientRegistrantRepository->ofRegistrantIdOrNull($event->getId());
        if ($clientRegistrant) {
            $clientRegistrant->generateInvoice($this->paymentGateway);
            $this->clientRegistrantRepository->update();
        }
    }

}

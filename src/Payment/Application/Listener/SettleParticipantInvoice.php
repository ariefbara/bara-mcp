<?php

namespace Payment\Application\Listener;

use Resources\Application\Event\Event;
use Resources\Application\Event\Listener;
use Resources\Domain\Event\CommonEvent;

class SettleParticipantInvoice implements Listener
{

    /**
     * 
     * @var ParticipantInvoiceRepository
     */
    protected $participantInvoiceRepository;

    public function __construct(ParticipantInvoiceRepository $participantInvoiceRepository)
    {
        $this->participantInvoiceRepository = $participantInvoiceRepository;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(CommonEvent $event): void
    {
        $participantInvoice = $this->participantInvoiceRepository->ofId($event->getId());
        if ($participantInvoice) {
            $participantInvoice->settle();
            $this->participantInvoiceRepository->update();
        }
    }

}

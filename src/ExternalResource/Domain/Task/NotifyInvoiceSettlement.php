<?php

namespace ExternalResource\Domain\Task;

use Config\EventList;
use ExternalResource\Domain\Model\ExternalTask;
use Resources\Application\Event\AdvanceDispatcher;
use Resources\Domain\Event\CommonEvent;

class NotifyInvoiceSettlement implements ExternalTask
{

    /**
     * 
     * @var AdvanceDispatcher
     */
    protected $dispatcher;

    public function __construct(AdvanceDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * 
     * @param string $payload invoiceId
     * @return void
     */
    public function execute($payload): void
    {
        $event = new CommonEvent(EventList::PAYMENT_RECEIVED, $payload);
        $this->dispatcher->dispatchEvent($event);
    }

}

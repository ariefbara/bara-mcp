<?php

namespace Payment\Domain\Model;

use Config\EventList;
use Payment\Domain\Task\PaymentGatewayAccount\PaymentGatewayAccountTask;
use Resources\Domain\Event\CommonEvent;
use Resources\Domain\Model\EntityContainCommonEvents;

class PaymentGatewayAccount extends EntityContainCommonEvents
{

    public function __construct()
    {
        
    }

    public function executePaymentGatewayAccountTask(PaymentGatewayAccountTask $task, $payload): void
    {
        $task->execute($this, $payload);
    }

    public function settleInvoice(string $invoiceId): void
    {
        $event = new CommonEvent(EventList::INVOICE_SETTLED, $invoiceId);
        $this->recordEvent($event);
    }

}

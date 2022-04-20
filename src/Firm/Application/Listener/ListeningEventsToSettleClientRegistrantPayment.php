<?php

namespace Firm\Application\Listener;

use Firm\Application\Service\ExecuteResponsiveTask;
use Firm\Domain\Task\Responsive\SettleClientRegistrantInvoicePayment;
use Firm\Domain\Task\Responsive\SettleRegistrantPayment;
use Resources\Application\Event\Event;
use Resources\Application\Event\Listener;
use Resources\Domain\Event\CommonEvent;

class ListeningEventsToSettleClientRegistrantPayment implements Listener
{

    /**
     * 
     * @var ExecuteResponsiveTask
     */
    protected $executeResponsiveTaskService;

    /**
     * 
     * @var SettleClientRegistrantInvoicePayment
     */
    protected $settleClientRegistrantInvoicePayment;

    public function __construct(
            ExecuteResponsiveTask $executeResponsiveTaskService,
            SettleClientRegistrantInvoicePayment $settleClientRegistrantInvoicePayment)
    {
        $this->executeResponsiveTaskService = $executeResponsiveTaskService;
        $this->settleClientRegistrantInvoicePayment = $settleClientRegistrantInvoicePayment;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(CommonEvent $event): void
    {
        $invoiceId = $event->getId();
        $this->executeResponsiveTaskService->execute($this->settleClientRegistrantInvoicePayment, $invoiceId);
    }

}

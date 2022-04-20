<?php

namespace Firm\Application\Listener;

use Config\EventList;
use Firm\Application\Service\ExecuteResponsiveTask;
use Firm\Domain\Task\Responsive\SettleClientRegistrantInvoicePayment;
use Resources\Domain\Event\CommonEvent;
use Tests\TestBase;

class ListeningEventsToSettleClientRegistrantPaymentTest extends TestBase
{
    protected $service;
    protected $task;
    protected $listener;
    protected $event, $invoiceId = 'invoice-id';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->buildMockOfClass(ExecuteResponsiveTask::class);
        $this->task = $this->buildMockOfClass(SettleClientRegistrantInvoicePayment::class);
        $this->listener = new ListeningEventsToSettleClientRegistrantPayment($this->service, $this->task);
        
        $this->event = new CommonEvent(EventList::PAYMENT_RECEIVED, $this->invoiceId);
    }
    
    protected function handle()
    {
        $this->listener->handle($this->event);
    }
    public function test_handle_executeService()
    {
        $this->service->expects($this->once())
                ->method('execute')
                ->with($this->task, $this->invoiceId);
        $this->handle();
    }
}

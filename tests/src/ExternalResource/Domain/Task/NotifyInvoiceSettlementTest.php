<?php

namespace ExternalResource\Domain\Task;

use Tests\TestBase;

class NotifyInvoiceSettlementTest extends TestBase
{
    protected $dispatcher;
    protected $task;
    protected $payload = 'invoice-id';


    protected function setUp(): void
    {
        parent::setUp();
        $this->dispatcher = $this->buildMockOfClass(\Resources\Application\Event\AdvanceDispatcher::class);
        $this->task = new NotifyInvoiceSettlement($this->dispatcher);
    }
    
    protected function execute()
    {
        $this->task->execute($this->payload);
    }
    public function test_execute_dispatchInvoiceSettledCommonEvent()
    {
        $event = new \Resources\Domain\Event\CommonEvent(\Config\EventList::PAYMENT_RECEIVED, $this->payload);
        $this->dispatcher->expects($this->once())
                ->method('dispatchEvent')
                ->with($event);
        $this->execute();
    }
}

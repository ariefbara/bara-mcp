<?php

namespace Firm\Application\Listener;

use Config\EventList;
use Firm\Domain\Model\Firm\Client\ClientRegistrant;
use Resources\Domain\Event\CommonEvent;
use Tests\TestBase;

class SettleClientRegistrantInvoicePaymentTest extends TestBase
{
    protected $clientRegistrantRepository;
    protected $clientRegistrant;
    protected $invoiceId = 'clientRegistrantId';
    protected $listener;
    protected $event;
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRegistrantRepository = $this->buildMockOfInterface(ClientRegistrantRepository::class);
        $this->clientRegistrant = $this->buildMockOfClass(ClientRegistrant::class);
        
        $this->listener = new SettleClientRegistrantInvoicePayment($this->clientRegistrantRepository);
        
        $this->event = new CommonEvent(EventList::PAYMENT_RECEIVED, $this->invoiceId);
    }
    
    protected function handle()
    {
        $this->clientRegistrantRepository->expects($this->any())
                ->method('aClientRegistrantOwningInvoiceId')
                ->with($this->invoiceId)
                ->willReturn($this->clientRegistrant);
        $this->listener->handle($this->event);
    }
    public function test_handle_settleClientRegistrantInvoicePayment()
    {
        $this->clientRegistrant->expects($this->once())
                ->method('settleInvoicePayment');
        $this->handle();
    }
    public function test_handle_updateRepository()
    {
        $this->clientRegistrantRepository->expects($this->once())
                ->method('update');
        $this->handle();
    }
    public function test_handle_noClientRegistrantCorrespondWithInvoiceFound_void()
    {
        $this->clientRegistrantRepository->expects($this->once())
                ->method('aClientRegistrantOwningInvoiceId')
                ->with($this->invoiceId)
                ->willReturn(null);
        $this->clientRegistrantRepository->expects($this->never())
                ->method('update');
        $this->handle();
    }
}

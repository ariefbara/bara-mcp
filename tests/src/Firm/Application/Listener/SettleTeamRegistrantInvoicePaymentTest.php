<?php

namespace Firm\Application\Listener;

use Config\EventList;
use Firm\Domain\Model\Firm\Team\TeamRegistrant;
use Resources\Domain\Event\CommonEvent;
use Tests\TestBase;

class SettleTeamRegistrantInvoicePaymentTest extends TestBase
{
    protected $teamRegistrantRepository;
    protected $teamRegistrant;
    protected $invoiceId = 'teamRegistrantId';
    protected $listener;
    protected $event;
    protected function setUp(): void
    {
        parent::setUp();
        $this->teamRegistrantRepository = $this->buildMockOfInterface(TeamRegistrantRepository::class);
        $this->teamRegistrant = $this->buildMockOfClass(TeamRegistrant::class);
        
        $this->listener = new SettleTeamRegistrantInvoicePayment($this->teamRegistrantRepository);
        
        $this->event = new CommonEvent(EventList::PAYMENT_RECEIVED, $this->invoiceId);
    }
    
    protected function handle()
    {
        $this->teamRegistrantRepository->expects($this->any())
                ->method('aTeamRegistrantOwningInvoiceId')
                ->with($this->invoiceId)
                ->willReturn($this->teamRegistrant);
        $this->listener->handle($this->event);
    }
    public function test_handle_settleTeamRegistrantInvoicePayment()
    {
        $this->teamRegistrant->expects($this->once())
                ->method('settleInvoicePayment');
        $this->handle();
    }
    public function test_handle_updateRepository()
    {
        $this->teamRegistrantRepository->expects($this->once())
                ->method('update');
        $this->handle();
    }
    public function test_handle_noTeamRegistrantCorrespondWithInvoiceFound_void()
    {
        $this->teamRegistrantRepository->expects($this->once())
                ->method('aTeamRegistrantOwningInvoiceId')
                ->with($this->invoiceId)
                ->willReturn(null);
        $this->teamRegistrantRepository->expects($this->never())
                ->method('update');
        $this->handle();
    }
}

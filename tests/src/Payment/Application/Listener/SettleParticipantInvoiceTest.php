<?php

namespace Payment\Application\Listener;

use Config\EventList;
use Payment\Domain\Model\Firm\Program\Participant\ParticipantInvoice;
use Resources\Domain\Event\CommonEvent;
use Tests\TestBase;

class SettleParticipantInvoiceTest extends TestBase
{
    protected $participantInvoiceRepository;
    protected $participantInvoice;
    protected $participantInvoiceId = 'participantINvoiceId';
    
    protected $listener;
    protected $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participantInvoiceRepository = $this->buildMockOfInterface(ParticipantInvoiceRepository::class);
        $this->participantInvoice = $this->buildMockOfClass(ParticipantInvoice::class);
        
        $this->listener = new SettleParticipantInvoice($this->participantInvoiceRepository);
        
        $this->event = new CommonEvent(EventList::INVOICE_SETTLED, $this->participantInvoiceId);
    }
    
    protected function handle()
    {
        $this->participantInvoiceRepository->expects($this->any())
                ->method('ofId')
                ->with($this->participantInvoiceId)
                ->willReturn($this->participantInvoice);
        $this->listener->handle($this->event);
    }
    public function test_handel_settleParticipantInvoice()
    {
        $this->participantInvoice->expects($this->once())
                ->method('settle');
        $this->handle();
    }
    public function test_handle_updateRepository()
    {
        $this->participantInvoiceRepository->expects($this->once())
                ->method('update');
        $this->handle();
    }
    public function test_handle_noParticipantInvoiceFound_void()
    {
        $this->participantInvoiceRepository->expects($this->once())
                ->method('ofId')
                ->with($this->participantInvoiceId)
                ->willReturn(null);
        $this->handle();
    }
}

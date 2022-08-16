<?php

namespace Payment\Application\Listener;

use Config\EventList;
use Payment\Domain\Model\Firm\Client\ClientParticipant;
use Resources\Domain\Event\CommonEvent;
use SharedContext\Domain\Task\Dependency\PaymentGateway;
use Tests\TestBase;

class GenerateClientParticipantInvoiceTest extends TestBase
{
    protected $clientParticipantRepository, $clientParticipant, $clientParticipantId = 'clientParticipantId';
    protected $paymentGateway;
    protected $listener;
    protected $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientParticipantRepository = $this->buildMockOfInterface(ClientParticipantRepository::class);
        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        
        $this->paymentGateway = $this->buildMockOfInterface(PaymentGateway::class);
        
        $this->listener = new GenerateClientParticipantInvoice($this->clientParticipantRepository, $this->paymentGateway);
        
        $this->event = new CommonEvent(EventList::SETTLEMENT_REQUIRED, $this->clientParticipantId);
    }
    
    protected function handle()
    {
        $this->clientParticipantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->clientParticipantId)
                ->willReturn($this->clientParticipant);
        $this->listener->handle($this->event);
    }
    public function test_handle_generateClientParticipantRepository()
    {
        $this->clientParticipant->expects($this->once())
                ->method('generateInvoice')
                ->with($this->paymentGateway);
        $this->handle();
    }
    public function test_handle_updateRepository()
    {
        $this->clientParticipantRepository->expects($this->once())
                ->method('update');
        $this->handle();
    }
    public function test_handle_clientParticipantNotFound()
    {
        $this->clientParticipantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->clientParticipantId)
                ->willReturn(null);
        $this->handle();
        $this->markAsSuccess();
    }
}

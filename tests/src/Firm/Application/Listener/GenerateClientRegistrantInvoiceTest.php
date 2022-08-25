<?php

namespace Firm\Application\Listener;

use Config\EventList;
use Firm\Domain\Model\Firm\Client\ClientRegistrant;
use Resources\Domain\Event\CommonEvent;
use SharedContext\Domain\Task\Dependency\PaymentGateway;
use Tests\TestBase;

class GenerateClientRegistrantInvoiceTest extends TestBase
{
    protected $clientRegistrantRepository;
    protected $clientRegistrant;
    protected $paymentGateway;
    protected $listener;
    
    protected $registrantId = 'registrantId';
    protected $event;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRegistrantRepository = $this->buildMockOfInterface(ClientRegistrantRepository::class);
        $this->clientRegistrant = $this->buildMockOfClass(ClientRegistrant::class);
        $this->paymentGateway = $this->buildMockOfInterface(PaymentGateway::class);
        $this->listener = new GenerateClientRegistrantInvoice($this->clientRegistrantRepository, $this->paymentGateway);
        
        $this->event = new CommonEvent(EventList::SETTLEMENT_REQUIRED, $this->registrantId);
    }
    
    protected function handle()
    {
        $this->clientRegistrantRepository->expects($this->any())
                ->method('ofRegistrantIdOrNull')
                ->with($this->registrantId)
                ->willReturn($this->clientRegistrant);
        $this->listener->handle($this->event);
    }
    public function test_handle_generateClientRegistrantInvoice()
    {
        $this->clientRegistrant->expects($this->once())
                ->method('generateInvoice')
                ->with($this->paymentGateway);
        $this->handle();
    }
    public function test_handle_updateRepository()
    {
        $this->clientRegistrantRepository->expects($this->once())
                ->method('update');
        $this->handle();
    }
    public function test_handle_noClientRegistrantFound_doNothing()
    {
        $this->clientRegistrantRepository->expects($this->any())
                ->method('ofRegistrantIdOrNull')
                ->with($this->registrantId)
                ->willReturn(null);
        $this->clientRegistrantRepository->expects($this->never())
                ->method('update');
        $this->handle();
        $this->markAsSuccess();
        
    }
}

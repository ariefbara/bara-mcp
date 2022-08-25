<?php

namespace Firm\Application\Listener;

use Config\EventList;
use Firm\Domain\Model\Firm\Team\TeamRegistrant;
use Resources\Domain\Event\CommonEvent;
use SharedContext\Domain\Task\Dependency\PaymentGateway;
use Tests\TestBase;

class GenerateTeamRegistrantInvoiceTest extends TestBase
{
    protected $teamRegistrantRepository;
    protected $teamRegistrant;
    protected $paymentGateway;
    protected $listener;
    
    protected $registrantId = 'registrantId';
    protected $event;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->teamRegistrantRepository = $this->buildMockOfInterface(TeamRegistrantRepository::class);
        $this->teamRegistrant = $this->buildMockOfClass(TeamRegistrant::class);
        $this->paymentGateway = $this->buildMockOfInterface(PaymentGateway::class);
        $this->listener = new GenerateTeamRegistrantInvoice($this->teamRegistrantRepository, $this->paymentGateway);
        
        $this->event = new CommonEvent(EventList::SETTLEMENT_REQUIRED, $this->registrantId);
    }
    
    protected function handle()
    {
        $this->teamRegistrantRepository->expects($this->any())
                ->method('ofRegistrantIdOrNull')
                ->with($this->registrantId)
                ->willReturn($this->teamRegistrant);
        $this->listener->handle($this->event);
    }
    public function test_handle_generateTeamRegistrantInvoice()
    {
        $this->teamRegistrant->expects($this->once())
                ->method('generateInvoice')
                ->with($this->paymentGateway);
        $this->handle();
    }
    public function test_handle_updateRepository()
    {
        $this->teamRegistrantRepository->expects($this->once())
                ->method('update');
        $this->handle();
    }
    public function test_handle_noTeamRegistrantFound_doNothing()
    {
        $this->teamRegistrantRepository->expects($this->any())
                ->method('ofRegistrantIdOrNull')
                ->with($this->registrantId)
                ->willReturn(null);
        $this->teamRegistrantRepository->expects($this->never())
                ->method('update');
        $this->handle();
        $this->markAsSuccess();
        
    }
}

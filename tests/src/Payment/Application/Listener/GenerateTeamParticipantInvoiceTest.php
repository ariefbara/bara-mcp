<?php

namespace Payment\Application\Listener;

use Config\EventList;
use Payment\Domain\Model\Firm\Team\TeamParticipant;
use Resources\Domain\Event\CommonEvent;
use SharedContext\Domain\Task\Dependency\PaymentGateway;
use Tests\TestBase;

class GenerateTeamParticipantInvoiceTest extends TestBase
{
    protected $teamParticipantRepository;
    protected $teamParticipant;
    protected $teamParticipantId = 'teamParticipantId';
    protected $paymentGateway;
    protected $listener;
    protected $event;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->teamParticipantRepository = $this->buildMockOfInterface(TeamParticipantRepository::class);
        $this->teamParticipant = $this->buildMockOfClass(TeamParticipant::class);
        $this->paymentGateway = $this->buildMockOfInterface(PaymentGateway::class);
        $this->listener = new GenerateTeamParticipantInvoice($this->teamParticipantRepository, $this->paymentGateway);
        
        $this->event = new CommonEvent(EventList::SETTLEMENT_REQUIRED, $this->teamParticipantId);
    }
    
    protected function handle()
    {
        $this->teamParticipantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->teamParticipantId)
                ->willReturn($this->teamParticipant);
        $this->listener->handle($this->event);
    }
    public function test_handle_generateTeamParticipantInvoice()
    {
        $this->teamParticipant->expects($this->once())
                ->method('generateInvoice')
                ->with($this->paymentGateway);
        $this->handle();
    }
    public function test_handle_noTeamParticipantFound_void()
    {
        $this->teamParticipantRepository->expects($this->any())
                ->method('ofId')
                ->willReturn(null);
        $this->teamParticipant->expects($this->never())
                ->method('generateInvoice')
                ->with($this->paymentGateway);
        $this->handle();
    }
    public function test_handle_updateRepository()
    {
        $this->teamParticipantRepository->expects($this->once())
                ->method('update');
        $this->handle();
    }
}

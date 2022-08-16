<?php

namespace Payment\Domain\Model\Firm\Team;

use Payment\Domain\Model\Firm\Program\Participant;
use Payment\Domain\Model\Firm\Team;
use SharedContext\Domain\Task\Dependency\PaymentGateway;
use SharedContext\Domain\ValueObject\CustomerInfo;
use Tests\TestBase;

class TeamParticipantTest extends TestBase
{

    protected $teamParticipant, $team, $participant;
    //
    protected $paymentGateway;
    protected $customerInfo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamParticipant = new TestableTeamParticipant();

        $this->team = $this->buildMockOfClass(Team::class);
        $this->teamParticipant->team = $this->team;

        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->teamParticipant->participant = $this->participant;
        //
        $this->paymentGateway = $this->buildMockOfInterface(PaymentGateway::class);
        
        $this->customerInfo = $this->buildMockOfClass(CustomerInfo::class);
    }
    
    protected function generateInvoice()
    {
        $this->team->expects($this->any())
                ->method('generateCustomerInfo')
                ->willReturn($this->customerInfo);
        $this->teamParticipant->generateInvoice($this->paymentGateway);
    }
    public function test_generateInvoice_generateParticipantInvoice()
    {
        $this->participant->expects($this->once())
                ->method('generateInvoice')
                ->with($this->paymentGateway, $this->customerInfo);
        $this->generateInvoice();
    }

}

class TestableTeamParticipant extends TeamParticipant
{

    public $team;
    public $id = 'teamParticipantId';
    public $participant;

    function __construct()
    {
        parent::__construct();
    }

}

<?php

namespace Payment\Domain\Model\Firm\Team;

use Payment\Domain\Model\Firm\Program\Participant;
use Payment\Domain\Model\Firm\Team;
use SharedContext\Domain\Task\Dependency\PaymentGateway;

class TeamParticipant
{

    /**
     * 
     * @var Team
     */
    protected $team;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var Participant
     */
    protected $participant;

    protected function __construct()
    {
        
    }

    public function generateInvoice(PaymentGateway $paymentGateway): void
    {
        $customerInfo = $this->team->generateCustomerInfo();
        $this->participant->generateInvoice($paymentGateway, $customerInfo);
    }

}

<?php

namespace Firm\Domain\Model\Firm\Team;

use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\Registrant;
use Firm\Domain\Model\Firm\Team;
use SharedContext\Domain\Task\Dependency\PaymentGateway;

class TeamRegistrant
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
     * @var Registrant
     */
    protected $registrant;

    public function __construct(Team $team, string $id, Registrant $registrant)
    {
        $this->team = $team;
        $this->id = $id;
        $this->registrant = $registrant;
    }

    public function isUnconcludedRegistrationInProgram(Program $program): bool
    {
        return $this->registrant->isUnconcludedRegistrationInProgram($program);
    }

    public function generateInvoice(PaymentGateway $paymentGateway): void
    {
        $customerInfo = $this->team->getCustomerInfo();
        $this->registrant->generateInvoice($paymentGateway, $customerInfo);
    }
    
    public function settleInvoicePayment(): void
    {
        $this->registrant->settleInvoicePayment($this->team);
    }
    
    public function addAsProgramParticipant(): void
    {
        $this->registrant->addApplicantAsParticipant($this->team);
    }

}

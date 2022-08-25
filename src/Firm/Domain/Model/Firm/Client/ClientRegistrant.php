<?php

namespace Firm\Domain\Model\Firm\Client;

use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\Registrant;
use SharedContext\Domain\Task\Dependency\PaymentGateway;

class ClientRegistrant
{

    /**
     * 
     * @var Client
     */
    protected $client;

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

    public function __construct(Client $client, string $id, Registrant $registrant)
    {
        $this->client = $client;
        $this->id = $id;
        $this->registrant = $registrant;
    }

    
    public function generateInvoice(PaymentGateway $paymentGateway): void
    {
        $this->registrant->generateInvoice($paymentGateway, $this->client->getClientCustomerInfo());
    }
    
    public function settleInvoicePayment(): void
    {
        $this->registrant->settleInvoicePayment($this->client);
    }
    
    public function isUnconcludedRegistrationInProgram(Program $program): bool
    {
        return $this->registrant->isUnconcludedRegistrationInProgram($program);
    }
    
    public function addAsProgramParticipant(): void
    {
        $this->registrant->addApplicantAsParticipant($this->client);
    }

}

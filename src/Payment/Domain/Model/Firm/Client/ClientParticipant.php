<?php

namespace Payment\Domain\Model\Firm\Client;

use Payment\Domain\Model\Firm\Client;
use Payment\Domain\Model\Firm\Program\Participant;
use SharedContext\Domain\Task\Dependency\PaymentGateway;

class ClientParticipant
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
     * @var Participant
     */
    protected $participant;

    protected function __construct()
    {
        
    }
    
    public function generateInvoice(PaymentGateway $paymentGateway): void
    {
        $this->participant->generateInvoice($paymentGateway, $this->client->generateCustomerInfo());
    }

}

<?php

namespace Firm\Domain\Model\Firm\Client;

use Firm\Domain\Model\Firm\Client;
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

    protected function __construct()
    {
        
    }

    public function generateInvoice(PaymentGateway $paymentGateway): void
    {
        $this->registrant->generateInvoice($paymentGateway, $this->client->getClientCustomerInfo());
    }
    
    public function settleInvoicePayment(): void
    {
        $this->registrant->settleInvoicePayment($this->client);
    }

}

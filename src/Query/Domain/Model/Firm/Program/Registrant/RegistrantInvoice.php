<?php

namespace Query\Domain\Model\Firm\Program\Registrant;

use Query\Domain\Model\Firm\Program\Registrant;
use SharedContext\Domain\Model\Invoice;

class RegistrantInvoice
{

    /**
     * 
     * @var Registrant
     */
    protected $registrant;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var Invoice
     */
    protected $invoice;

    public function getRegistrant(): Registrant
    {
        return $this->registrant;
    }

    public function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        
    }

    public function getIssuedTimeString(): string
    {
        return $this->invoice->getIssuedTimeString();
    }

    public function getExpiredTimeString(): string
    {
        return $this->invoice->getExpiredTimeString();
    }

    public function getPaymentLink(): string
    {
        return $this->invoice->getPaymentLink();
    }

    public function isSettled(): bool
    {
        return $this->invoice->isSettled();
    }

}

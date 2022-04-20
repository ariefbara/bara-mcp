<?php

namespace Firm\Domain\Model\Firm\Program\Registrant;

use Firm\Domain\Model\Firm\Program\Registrant;
use Resources\Domain\Model\EntityContainEvents;
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
    
    public function __construct(Registrant $registrant, string $id, Invoice $invoice)
    {
        $this->registrant = $registrant;
        $this->id = $id;
        $this->invoice = $invoice;
    }
    
    public function settle(): void
    {
        $this->invoice->settle();
    }


}

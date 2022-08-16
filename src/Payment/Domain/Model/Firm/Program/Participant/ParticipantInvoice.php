<?php

namespace Payment\Domain\Model\Firm\Program\Participant;

use Payment\Domain\Model\Firm\Program\Participant;
use SharedContext\Domain\Model\Invoice;

class ParticipantInvoice
{

    /**
     * 
     * @var Participant
     */
    protected $participant;

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

    public function __construct(Participant $participant, string $id, Invoice $invoice)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->invoice = $invoice;
    }

    public function settle(): void
    {
        $this->invoice->settle();
        $this->participant->settlementCompleted();
    }

}

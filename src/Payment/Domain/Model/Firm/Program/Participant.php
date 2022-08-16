<?php

namespace Payment\Domain\Model\Firm\Program;

use Payment\Domain\Model\Firm\Program;
use Payment\Domain\Model\Firm\Program\Participant\ParticipantInvoice;
use Resources\Exception\RegularException;
use SharedContext\Domain\Task\Dependency\InvoiceParameter;
use SharedContext\Domain\Task\Dependency\PaymentGateway;
use SharedContext\Domain\ValueObject\CustomerInfo;
use SharedContext\Domain\ValueObject\ItemInfo;
use SharedContext\Domain\ValueObject\ParticipantStatus;

class Participant
{

    /**
     * 
     * @var Program
     */
    protected $program;

    /**
     * 
     * @var string
     */
    protected $id;
    
    /**
     * 
     * @var ParticipantStatus
     */
    protected $status;

    /**
     * 
     * @var int|null
     */
    protected $programPrice;

    /**
     * 
     * @var ParticipantInvoice|null
     */
    protected $participantInvoice;

    protected function __construct()
    {
        
    }

    public function generateInvoice(PaymentGateway $paymentGateway, CustomerInfo $customerInfo): void
    {
        if ($this->participantInvoice) {
            throw RegularException::forbidden('item can only has one invoice');
        }
        $description = "invoice pendaftaran program: {$this->program->getName()}";
        $duration = 7 * 24 * 60 * 60;
        $itemInfo = new ItemInfo($this->program->getName(), 1, $this->programPrice, null, null);
        $invoiceParameter = new InvoiceParameter(
                $this->id, $this->programPrice, $description, $duration, $customerInfo, $itemInfo);
        $invoice = $paymentGateway->generateInvoice($this->id, $invoiceParameter);
        $this->participantInvoice = new ParticipantInvoice($this, $this->id, $invoice);
//        $description = "invoice pendaftaran program: {$this->program->getName()}";
//        $duration = 7 * 24 * 60 * 60;
//        $itemInfo = new ItemInfo($this->program->getName(), 1, $this->programPrice, null, null);
//
//        $invoiceParameter = new InvoiceParameter(
//                $this->id, $this->programPrice, $description, $duration, $customerInfo, $itemInfo);
//        $invoice = $paymentGateway->generateInvoice($this->id, $invoiceParameter);
//        $this->participantInvoice = new ParticipantInvoice($this, $this->id, $invoice);
    }
    
    public function settlementCompleted(): void
    {
        $this->status = $this->status->settlePayment();
    }

}

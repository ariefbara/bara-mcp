<?php

namespace SharedContext\Domain\Task\Dependency;

use SharedContext\Domain\Model\Invoice;

interface PaymentGateway
{

    public function generateInvoiceLink(InvoiceParameter $invoiceParameter): string;

    public function generateInvoice(string $id, InvoiceParameter $invoiceParameter): Invoice;
}

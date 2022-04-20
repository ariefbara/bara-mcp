<?php

namespace SharedContext\Domain\Task\Dependency;

interface PaymentGateway
{
    public function generateInvoiceLink(InvoiceParameter $invoiceParameter): string;
}

<?php

namespace SharedContext\Infrastructure\Xendit;

use Config\BaseConfig;
use SharedContext\Domain\Task\Dependency\InvoiceParameter;
use SharedContext\Domain\Task\Dependency\PaymentGateway;
use Xendit\Xendit;

class XenditPaymentGateway implements PaymentGateway
{

    public function generateInvoiceLink(InvoiceParameter $invoiceParameter): string
    {
        Xendit::setApiKey(env('XENDIT_API_KEY'));
        $params = $invoiceParameter->toArray();
        $createdInvoice = \Xendit\Invoice::create($params);
        return $createdInvoice['invoice_url'];
    }

}

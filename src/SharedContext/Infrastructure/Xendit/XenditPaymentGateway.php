<?php

namespace SharedContext\Infrastructure\Xendit;

use Resources\DateTimeImmutableBuilder;
use SharedContext\Domain\Model\Invoice;
use SharedContext\Domain\Task\Dependency\InvoiceParameter;
use SharedContext\Domain\Task\Dependency\PaymentGateway;
use Xendit\Xendit;
use function env;

class XenditPaymentGateway implements PaymentGateway
{

    public function generateInvoiceLink(InvoiceParameter $invoiceParameter): string
    {
        Xendit::setApiKey(env('XENDIT_API_KEY'));
        $params = $invoiceParameter->toArray();
        $createdInvoice = \Xendit\Invoice::create($params);
        return $createdInvoice['invoice_url'];
    }

    public function generateInvoice(string $id, InvoiceParameter $invoiceParameter): Invoice
    {
        $expiredTime = DateTimeImmutableBuilder::buildYmdHisAccuracy('+7 days');
        $paymentLink = $this->generateInvoiceLink($invoiceParameter);
        return new Invoice($id, $expiredTime, $paymentLink);
    }

}

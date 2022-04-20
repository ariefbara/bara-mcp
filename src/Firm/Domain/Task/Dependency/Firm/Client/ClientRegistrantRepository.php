<?php

namespace Firm\Domain\Task\Dependency\Firm\Client;

use Firm\Domain\Model\Firm\Client\ClientRegistrant;

interface ClientRegistrantRepository
{

    public function ofId(string $id): ClientRegistrant;

    public function aClientRegistrantOwningInvoiceId(string $invoiceId): ClientRegistrant;
}

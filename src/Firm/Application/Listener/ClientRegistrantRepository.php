<?php

namespace Firm\Application\Listener;

use Firm\Domain\Model\Firm\Client\ClientRegistrant;

interface ClientRegistrantRepository
{

    public function ofRegistrantIdOrNull(string $registrantId): ?ClientRegistrant;

    public function aClientRegistrantOwningInvoiceId(string $invoiceId): ?ClientRegistrant;

    public function update(): void;
}

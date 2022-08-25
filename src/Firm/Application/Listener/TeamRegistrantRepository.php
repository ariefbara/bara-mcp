<?php

namespace Firm\Application\Listener;

use Firm\Domain\Model\Firm\Team\TeamRegistrant;

interface TeamRegistrantRepository
{

    public function ofRegistrantIdOrNull(string $registrantId): ?TeamRegistrant;
    
    public function aTeamRegistrantOwningInvoiceId(string $invoiceId): ?TeamRegistrant;

    public function update(): void;
}

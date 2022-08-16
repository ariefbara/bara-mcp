<?php

namespace Payment\Application\Listener;

use Payment\Domain\Model\Firm\Program\Participant\ParticipantInvoice;

interface ParticipantInvoiceRepository
{

    public function ofId(string $participantInvoiceId): ?ParticipantInvoice;

    public function update(): void;
}

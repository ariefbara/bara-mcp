<?php

namespace Query\Application\Auth\Firm;

interface ClientRepository
{
    public function containRecordOfActiveClientInFirm(string $firmId, string $clientId): bool;
}

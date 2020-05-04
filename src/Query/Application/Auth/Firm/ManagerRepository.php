<?php

namespace Query\Application\Auth\Firm;

interface ManagerRepository
{
    public function containRecordOfId(string $firmId, string $managerId): bool;
}

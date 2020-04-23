<?php

namespace Firm\Application\Auth;

interface ManagerRepository
{
    public function containRecordOfId(string $firmId, string $managerId): bool;
}

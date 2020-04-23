<?php

namespace Bara\Application\Auth;

interface AdminRepository
{
    public function containRecordOfId(string $adminId): bool;
}

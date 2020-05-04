<?php

namespace Query\Application\Auth;

interface AdminRepository
{
    public function containRecordOfId(string $adminId): bool;
}

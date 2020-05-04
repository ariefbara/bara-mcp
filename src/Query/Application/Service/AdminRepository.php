<?php

namespace Query\Application\Service;

use Query\Domain\Model\Admin;

interface AdminRepository
{

    public function ofEmail(string $email): Admin;

    public function ofId(string $adminId): Admin;

    public function all(int $page, int $pageSize);
}

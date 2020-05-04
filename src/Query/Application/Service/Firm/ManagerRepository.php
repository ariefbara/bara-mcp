<?php

namespace Query\Application\Service\Firm;

use Query\Domain\Model\Firm\Manager;

interface ManagerRepository
{

    public function ofEmail(string $firmIdentifier, string $email): Manager;

    public function ofId(string $firmId, string $managerId): Manager;

    public function all(string $firmId, int $page, int $pageSize);
}

<?php

namespace Query\Application\Service\Manager;

use Query\Domain\Model\Firm\Manager;

interface ManagerRepository
{

    public function aManagerInFirm(string $firmId, string $managerId): Manager;
}

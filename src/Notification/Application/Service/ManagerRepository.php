<?php

namespace Notification\Application\Service;

use Notification\Domain\Model\Firm\Manager;

interface ManagerRepository
{
    public function ofId(string $managerId): Manager;
}

<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Manager;

interface ManagerRepository
{

    public function aManagerInFirm(string $firmId, string $managerId): Manager;
    
    public function update(): void;
}

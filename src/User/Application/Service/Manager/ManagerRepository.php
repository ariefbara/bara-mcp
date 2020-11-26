<?php

namespace User\Application\Service\Manager;

use User\Domain\Model\Manager;

interface ManagerRepository
{
    public function aManagerInFirm(string $firmId, string $managerId): Manager;
    
    public function aManagerInFirmByEmailAndIdentifier(string $firmIdentifier, string $email): Manager;
    
    public function update(): void;
}

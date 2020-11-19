<?php

namespace Firm\Application\Service\Firm;

use Firm\ {
    Application\Service\Manager\ManagerRepository as InterfaceForManager,
    Domain\Model\Firm\Manager
};

interface ManagerRepository extends InterfaceForManager
{

    public function nextIdentity(): string;

    public function add(Manager $manager): void;

    public function isEmailAvailable(string $firmId, string $email): bool;

    public function ofId(string $firmId, string $managerId): Manager;
    
    public function aManagerOfId(string $managerId): Manager;
}

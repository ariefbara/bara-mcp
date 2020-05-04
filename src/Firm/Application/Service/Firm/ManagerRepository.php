<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\Firm\Manager;

interface ManagerRepository
{

    public function nextIdentity(): string;

    public function add(Manager $manager): void;

    public function isEmailAvailable(string $firmId, string $email): bool;

    public function ofId(string $firmId, string $managerId): Manager;
}

<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\Firm\Manager;

interface ManagerRepository
{

    public function nextIdentity(): string;

    public function add(Manager $manager): void;

    public function isEmailAvailable(string $firmId, string $email): bool;

    public function ofEmail(string $firmIdentifier, string $email): Manager;

    public function ofId(string $firmId, string $managerId): Manager;

    public function all(string $firmId, int $page, int $pageSize);
}

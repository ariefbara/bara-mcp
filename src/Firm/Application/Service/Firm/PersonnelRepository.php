<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\Firm\Personnel;

interface PersonnelRepository
{

    public function nextIdentity(): string;

    public function add(Personnel $personnel): void;

    public function isEmailAvailable(string $firmId, string $email): bool;

    public function ofId(string $firmId, string $personnelId): Personnel;
}

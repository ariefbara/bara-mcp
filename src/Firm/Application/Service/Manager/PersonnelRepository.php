<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Personnel;

interface PersonnelRepository
{

    public function aPersonnelOfId(string $personnelId): Personnel;

    public function update(): void;
}

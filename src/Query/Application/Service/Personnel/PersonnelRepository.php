<?php

namespace Query\Application\Service\Personnel;

use Query\Domain\Model\Firm\Personnel;

interface PersonnelRepository
{
    public function aPersonnelInFirm(string $firmId, string $personnelId): Personnel;
}

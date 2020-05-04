<?php

namespace Query\Application\Service\Firm;

use Query\Domain\Model\Firm\Personnel;

interface PersonnelRepository
{

    public function ofId(string $firmId, string $personnelId): Personnel;

    public function all(string $firmId, int $page, int $pageSize);

    public function ofEmail(string $firmIdentifier, string $email): Personnel;
}

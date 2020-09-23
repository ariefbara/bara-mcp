<?php

namespace Query\Application\Service\Firm;

use Query\Domain\Model\Firm\Team;

interface TeamRepository
{

    public function ofId(string $firmId, string $teamId): Team;

    public function all(string $firmId, int $page, int $pageSize);
}

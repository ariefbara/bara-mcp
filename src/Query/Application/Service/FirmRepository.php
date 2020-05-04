<?php

namespace Query\Application\Service;

use Query\Domain\Model\Firm;

interface FirmRepository
{

    public function ofId(string $firmId): Firm;

    public function all(int $page, int $pageSize);
}

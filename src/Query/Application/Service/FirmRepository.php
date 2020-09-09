<?php

namespace Query\Application\Service;

use Client\Application\Service\FirmRepository as InterfaceForClientBC;
use Query\Domain\Model\Firm;

interface FirmRepository extends InterfaceForClientBC
{

    public function ofId(string $firmId): Firm;

    public function all(int $page, int $pageSize);
}

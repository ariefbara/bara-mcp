<?php

namespace Firm\Application\Service;

use Firm\Domain\Model\Firm;

interface FirmRepository
{

    public function ofId(string $firmId): Firm;
}

<?php

namespace Client\Application\Service;

use Query\Domain\Model\Firm;

interface FirmRepository
{
    public function ofIdentifier(string $firmIdentifier): Firm;
}

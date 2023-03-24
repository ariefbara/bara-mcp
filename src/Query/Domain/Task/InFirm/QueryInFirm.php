<?php

namespace Query\Domain\Task\InFirm;

use Query\Domain\Model\Firm;

interface QueryInFirm
{

    public function executeQueryInFirm(Firm $firm, $payload): void;
}

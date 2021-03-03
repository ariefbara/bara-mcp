<?php

namespace Firm\Application\Service\Coordinator;

use Firm\Domain\Model\Firm\Program\Participant\OKRPeriod;

interface OKRPeriodRepository
{

    public function ofId(string $okrPeriodId): OKRPeriod;
}

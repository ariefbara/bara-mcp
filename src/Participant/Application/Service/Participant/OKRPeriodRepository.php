<?php

namespace Participant\Application\Service\Participant;

use Participant\Domain\Model\Participant\OKRPeriod;

interface OKRPeriodRepository
{

    public function nextIdentity(): string;

    public function add(OKRPeriod $okrPeriod): void;

    public function ofId(string $okrPeriodId): OKRPeriod;
}

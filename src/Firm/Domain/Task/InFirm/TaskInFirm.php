<?php

namespace Firm\Domain\Task\InFirm;

use Firm\Domain\Model\Firm;

interface TaskInFirm
{

    public function executeInFirm(Firm $firm, $payload): void;
}

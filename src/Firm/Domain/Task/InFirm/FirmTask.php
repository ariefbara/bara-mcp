<?php

namespace Firm\Domain\Task\InFirm;

use Firm\Domain\Model\Firm;

interface FirmTask
{

    public function execute(Firm $firm, $payload): void;
}

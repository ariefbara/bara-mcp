<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\Firm;

interface FirmTaskExecutableByManager
{
    public function executeInFirm(Firm $firm): void;
}

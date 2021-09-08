<?php

namespace Query\Domain\Model\Firm;

use Query\Domain\Model\Firm;

interface ITaskInFirmExecutableByManager
{
    public function executeTaskInFirm(Firm $firm): void;
}

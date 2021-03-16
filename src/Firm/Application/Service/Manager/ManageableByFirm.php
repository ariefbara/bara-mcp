<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm;

interface ManageableByFirm
{
    public function isManageableByFirm(Firm $firm): bool;
}

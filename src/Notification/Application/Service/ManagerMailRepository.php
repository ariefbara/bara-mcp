<?php

namespace Notification\Application\Service;

use Notification\Domain\Model\Firm\Manager\ManagerMail;

interface ManagerMailRepository
{
    public function nextIdentity(): string;
    
    public function add(ManagerMail $managerMail): void;
}

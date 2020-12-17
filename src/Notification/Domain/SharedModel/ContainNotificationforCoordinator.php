<?php

namespace Notification\Domain\SharedModel;

use Notification\Domain\Model\Firm\Program\Coordinator;

interface ContainNotificationforCoordinator
{
    public function addCoordinatorAsRecipient(Coordinator $coordinator): void;
}

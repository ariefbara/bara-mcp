<?php

namespace Notification\Domain\SharedModel;

use Notification\Domain\Model\Firm\Manager;

interface ContainNotificationForAllUser extends ContainNotification
{

    public function addManagerRecipient(Manager $manager): void;
}

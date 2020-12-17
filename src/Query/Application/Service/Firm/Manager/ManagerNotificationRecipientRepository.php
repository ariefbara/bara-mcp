<?php

namespace Query\Application\Service\Firm\Manager;

use Query\Domain\Model\Firm\Manager\ManagerNotificationRecipient;

interface ManagerNotificationRecipientRepository
{

    public function aNotificationForManager(string $firmId, string $managerId, string $managerNotificationId): ManagerNotificationRecipient;

    public function allNotificationForManager(string $firmId, string $managerId, int $page, int $pageSize);
}

<?php

namespace Personnel\Application\Listener;

use Personnel\Domain\Model\Firm\Personnel\PersonnelNotification;

interface PersonnelNotificationRepository
{

    public function nextIdentity(): string;

    public function add(PersonnelNotification $personnelNotification): string;
}

<?php

namespace User\Application\Listener;

use User\Domain\Model\User\UserNotification;

interface UserNotificationRepository
{

    public function nextIdentity(): string;

    public function add(UserNotification $userNotification): void;
}

<?php

namespace Query\Application\Service\User;

interface UserNotificationRepository
{

    public function allNotificationBelongsToUser(string $userId, int $page, int $pageSize, ?bool $readStatus);
}

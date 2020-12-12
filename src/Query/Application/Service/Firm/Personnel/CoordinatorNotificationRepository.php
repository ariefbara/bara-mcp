<?php

namespace Query\Application\Service\Firm\Personnel;

interface CoordinatorNotificationRepository
{

    public function allNotificationForCoordinator(
            string $firmId, string $personnelId, string $coordinatorId, int $page, int $pageSize);
}

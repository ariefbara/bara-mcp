<?php

namespace Query\Application\Service\Firm\Personnel;

interface PersonnelNotificationRepository
{

    public function allNotificationBelongsToPersonnel(string $personnelId, int $page, int $pageSize, ?bool $readStatus);
}

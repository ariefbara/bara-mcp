<?php

namespace Query\Application\Service\Firm\Personnel;

use Query\Domain\Model\Firm\Personnel\PersonnelNotification;

interface PersonnelNotificationRepository
{

    public function ofId(PersonnelCompositionId $personnelCompositionId, string $personnelNotificationId): PersonnelNotification;

    public function all(PersonnelCompositionId $personnelCompositionId, int $page, int $pageSize);
}

<?php

namespace Query\Application\Service\Firm\Manager;

use Query\Domain\Model\Firm\Manager\ManagerActivity;

interface ManagerActivityRepository
{

    public function anActivityBelongsToManager(string $firmId, string $managerId, string $activityId): ManagerActivity;

    public function allActivitiesBelongsToManager(string $firmId, string $managerId, int $page, int $pageSize);
}

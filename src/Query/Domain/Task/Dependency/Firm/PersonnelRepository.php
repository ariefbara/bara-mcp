<?php

namespace Query\Domain\Task\Dependency\Firm;

interface PersonnelRepository
{

    public function mentorDashboardSummary(string $personnelId);

    public function coordinatorDashboardSummary(string $personnelId);
}

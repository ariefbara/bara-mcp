<?php

namespace Query\Domain\Task\Dependency;

interface ScheduleRepository
{

    public function allScheduleBelongsToClient(string $clientId, ScheduleFilter $filter): array;
}

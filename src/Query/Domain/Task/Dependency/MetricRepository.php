<?php

namespace Query\Domain\Task\Dependency;

interface MetricRepository
{

    public function metricSummaryOfParticipant(string $participantId): ?array;
}

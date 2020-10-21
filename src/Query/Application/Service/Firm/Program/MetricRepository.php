<?php

namespace Query\Application\Service\Firm\Program;

use Query\Domain\Model\Firm\Program\Metric;

interface MetricRepository
{

    public function aMetricInProgram(string $programId, string $metricId): Metric;

    public function allMetricsInProgram(string $programId, int $page, int $pageSize);
}

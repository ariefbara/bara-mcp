<?php

namespace Firm\Domain\Service;

use Firm\Domain\Model\Firm\Program\Metric;

interface MetricRepository
{

    public function ofId(string $metricId): Metric;
}
